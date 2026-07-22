<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Mail\ResetPasswordMail;
use App\Models\Candidat;
use App\Support\OtpService;
use App\Support\Parametres;
use App\Support\PasswordResetService;
use App\Support\VilleResolver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur d'authentification des candidats
 * 
 * Gère l'inscription, la connexion et la déconnexion des candidats
 */
class CandidatAuthController extends Controller
{
    /**
     * Inscription d'un nouveau candidat (préinscription)
     * 
     * POST /api/candidats/register
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // Obligatoires
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date|before:today',
            'genre' => 'required|in:M,F',
            'email' => 'required|email|max:150|unique:candidats,email',
            'password' => 'required|string|min:8|confirmed',
            
            // Optionnels
            'nip' => 'nullable|string|max:50|unique:candidats,nip',
            'matricule' => 'nullable|string|max:50|unique:candidats,matricule',
            'lieu_naissance_id' => 'nullable|exists:ville,id',
            'ville_naissance_custom' => 'nullable|string|max:255',
            'pays_naissance_id' => 'nullable|exists:pays,id',
            'etat_civil' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'ville_residence_id' => 'nullable|exists:ville,id',
            
            // Photo (base64 ou fichier)
            'photo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);
            $data['statut'] = 'en_attente';

            $data['lieu_naissance_id'] = VilleResolver::resoudre(
                $data['lieu_naissance_id'] ?? null,
                $data['ville_naissance_custom'] ?? null,
                $data['pays_naissance_id'] ?? null
            );
            unset($data['ville_naissance_custom'], $data['pays_naissance_id']);

            // Gestion de la photo (si base64)
            if (isset($data['photo']) && $this->isBase64($data['photo'])) {
                $data['photo'] = $this->saveBase64Image($data['photo'], 'candidats/photos');
            }

            $candidat = Candidat::create($data);

            // Vérification d'email obligatoire par OTP avant toute connexion
            // (pas de token délivré ici : cf. verifyOtp/purpose=inscription).
            $code = OtpService::genererCode($candidat->email, 'candidat', 'inscription');

            try {
                Mail::to($candidat->email)->send(new OtpCodeMail($candidat->prenom, $code, 'inscription'));
            } catch (\Exception $e) {
                Log::warning('Echec envoi email OTP inscription', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'otp_required' => true,
                'purpose' => 'inscription',
                'email' => $candidat->email,
                'message' => 'Un code de vérification vient de vous être envoyé par email.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion d'un candidat
     * 
     * POST /api/candidats/login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $candidat = Candidat::where('email', $credentials['email'])->first();

        if (!$candidat || !Hash::check($credentials['password'], $candidat->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Vérifier si le candidat peut se connecter
        if (!$candidat->peutSeConnecter()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre candidature a été refusée. Vous ne pouvez plus accéder à votre compte.',
                'statut' => $candidat->statut,
                'motif_refus' => $candidat->motif_refus
            ], 403);
        }

        // Double authentification à la connexion, si activée par le Super
        // Administrateur (désactivée par défaut, cf. BLOC 14 du planning).
        if (Parametres::getBool(Parametres::OTP_LOGIN_CANDIDAT)) {
            $code = OtpService::genererCode($candidat->email, 'candidat', 'connexion');

            try {
                Mail::to($candidat->email)->send(new OtpCodeMail($candidat->prenom, $code, 'connexion'));
            } catch (\Exception $e) {
                Log::warning('Echec envoi email OTP connexion', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'otp_required' => true,
                'purpose' => 'connexion',
                'email' => $candidat->email,
                'message' => 'Un code de connexion vient de vous être envoyé par email.'
            ]);
        }

        return response()->json($this->emettreSessionCandidat($candidat));
    }

    /**
     * Vérifie un code OTP (inscription ou connexion) et délivre le token de
     * session une fois validé.
     *
     * POST /api/candidats/verify-otp
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'purpose' => 'required|in:inscription,connexion',
        ]);

        if (!OtpService::verifierCode($validated['email'], $validated['code'], 'candidat', $validated['purpose'])) {
            return response()->json(['success' => false, 'message' => 'Code invalide ou expiré.'], 422);
        }

        $candidat = Candidat::where('email', $validated['email'])->first();

        if (!$candidat) {
            return response()->json(['success' => false, 'message' => 'Candidat introuvable.'], 404);
        }

        if ($validated['purpose'] === 'inscription' && !$candidat->email_verifie_le) {
            $candidat->update(['email_verifie_le' => now()]);
        }

        return response()->json($this->emettreSessionCandidat($candidat));
    }

    /**
     * Renvoie un nouveau code OTP (inscription ou connexion).
     *
     * POST /api/candidats/resend-otp
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'purpose' => 'required|in:inscription,connexion',
        ]);

        $candidat = Candidat::where('email', $validated['email'])->first();

        if ($candidat) {
            $code = OtpService::genererCode($candidat->email, 'candidat', $validated['purpose']);

            try {
                Mail::to($candidat->email)->send(new OtpCodeMail($candidat->prenom, $code, $validated['purpose']));
            } catch (\Exception $e) {
                Log::warning('Echec renvoi email OTP', ['error' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Si ce compte existe, un nouveau code vient de vous être envoyé.']);
    }

    /**
     * Supprime les anciens tokens et en délivre un nouveau — factorisé car
     * appelé aussi bien par login() (OTP désactivé) que par verifyOtp()
     * (OTP activé, une fois le code validé).
     */
    private function emettreSessionCandidat(Candidat $candidat): array
    {
        $candidat->tokens()->delete();

        $token = $candidat->createToken('candidat-token', ['*'], now()->addDays(7))->plainTextToken;

        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'candidat' => $candidat->load(['lieuNaissance', 'villeResidence', 'documents']),
            'expires_at' => now()->addDays(7)->toIso8601String()
        ];
    }

    /**
     * Déconnexion d'un candidat
     * 
     * POST /api/candidats/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Obtenir le candidat connecté
     * 
     * GET /api/candidats/me
     */
    public function me(Request $request): JsonResponse
    {
        $candidat = $request->user();
        $candidat->load(['lieuNaissance', 'villeResidence', 'documents', 'validePar', 'dignitaire']);

        return response()->json([
            'success' => true,
            'candidat' => $candidat
        ]);
    }

    /**
     * Mettre à jour le profil du candidat (seulement si statut = en_attente)
     * 
     * PUT /api/candidats/me
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $candidat = $request->user();

        if (!$candidat->estModifiable()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre candidature ne peut plus être modifiée.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'date_naissance' => 'sometimes|date|before:today',
            'genre' => 'sometimes|in:M,F',
            'nip' => 'nullable|string|max:50|unique:candidats,nip,' . $candidat->id,
            'matricule' => 'nullable|string|max:50|unique:candidats,matricule,' . $candidat->id,
            'lieu_naissance_id' => 'nullable|exists:ville,id',
            'etat_civil' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'ville_residence_id' => 'nullable|exists:ville,id',
            'photo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Gestion de la photo
            if (isset($data['photo']) && $this->isBase64($data['photo'])) {
                // Supprimer l'ancienne photo si elle existe
                if ($candidat->photo) {
                    $this->deleteFile($candidat->photo);
                }
                $data['photo'] = $this->saveBase64Image($data['photo'], 'candidats/photos');
            }

            $candidat->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'candidat' => $candidat->fresh()->load(['lieuNaissance', 'villeResidence'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Changer le mot de passe du candidat connecté.
     *
     * Contrairement à updateProfile(), disponible quel que soit le statut de
     * la candidature (un compte doit toujours pouvoir changer son mot de
     * passe, y compris une fois le dossier validé/refusé).
     *
     * PUT /api/candidats/me/password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $candidat = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->current_password, $candidat->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect'
            ], 401);
        }

        $candidat->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe changé avec succès'
        ]);
    }

    /**
     * Demande de réinitialisation de mot de passe (candidat).
     *
     * Répond toujours avec succès, que l'email existe ou non, pour ne pas
     * révéler quels emails sont enregistrés.
     *
     * POST /api/candidats/forgot-password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $candidat = Candidat::where('email', $validated['email'])->first();

        if ($candidat) {
            $token = PasswordResetService::creerToken($candidat->email, 'candidat');
            $resetUrl = config('app.frontend_url') . '/candidature/reset-password?token=' . $token . '&email=' . urlencode($candidat->email) . '&type=candidat';

            if (!app()->isProduction()) {
                Log::info("Réinitialisation mot de passe [candidat] pour {$candidat->email} : {$resetUrl}");
            }

            try {
                Mail::to($candidat->email)->send(new ResetPasswordMail($candidat->prenom, $resetUrl));
            } catch (\Exception $e) {
                Log::warning('Echec envoi email de réinitialisation (candidat)', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Si cet email existe, un lien de réinitialisation vient de vous être envoyé.'
        ]);
    }

    /**
     * Réinitialisation effective du mot de passe via le token reçu par email.
     *
     * POST /api/candidats/reset-password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!PasswordResetService::verifierToken($validated['email'], $validated['token'], 'candidat')) {
            return response()->json(['success' => false, 'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'], 422);
        }

        $candidat = Candidat::where('email', $validated['email'])->first();

        if (!$candidat) {
            return response()->json(['success' => false, 'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'], 422);
        }

        $candidat->update(['password' => Hash::make($validated['password'])]);
        PasswordResetService::oublierToken($validated['email'], 'candidat');

        $candidat->tokens()->delete();

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé avec succès.']);
    }

    /**
     * Vérifier si une chaîne est en base64
     */
    private function isBase64(string $data): bool
    {
        return preg_match('/^data:image\/(\w+);base64,/', $data);
    }

    /**
     * Sauvegarder une image base64
     */
    private function saveBase64Image(string $base64, string $folder): string
    {
        // Extraire les données de l'image
        preg_match('/^data:image\/(\w+);base64,/', $base64, $matches);
        $extension = $matches[1] ?? 'jpg';
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $imageData = base64_decode($imageData);

        // Générer un nom unique
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $path = $folder . '/' . $filename;

        // Sauvegarder le fichier
        \Storage::disk('public')->put($path, $imageData);

        return $path;
    }

    /**
     * Supprimer un fichier
     */
    private function deleteFile(string $path): void
    {
        if (\Storage::disk('public')->exists($path)) {
            \Storage::disk('public')->delete($path);
        }
    }
}
