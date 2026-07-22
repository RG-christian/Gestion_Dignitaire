<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Support\OtpService;
use App\Support\Parametres;
use App\Support\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Connexion
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->load(['fonctions', 'sousfonctions']);

            // Double authentification à la connexion, si activée par le
            // Super Administrateur (désactivée par défaut, cf. BLOC 14).
            if (Parametres::getBool(Parametres::OTP_LOGIN_ADMIN)) {
                Auth::logout();

                $code = OtpService::genererCode($user->email, 'admin', 'connexion');

                try {
                    Mail::to($user->email)->send(new OtpCodeMail($user->nom_complet ?? $user->username, $code, 'connexion'));
                } catch (\Exception $e) {
                    Log::warning('Echec envoi email OTP connexion (admin)', ['error' => $e->getMessage()]);
                }

                return response()->json([
                    'otp_required' => true,
                    'purpose' => 'connexion',
                    'email' => $user->email,
                    'message' => 'Un code de connexion vient de vous être envoyé par email.'
                ]);
            }

            return response()->json($this->emettreSession($user));
        }

        return response()->json([
            'message' => 'Identifiants invalides'
        ], 401);
    }

    /**
     * Vérifie le code OTP de connexion et délivre le token de session.
     *
     * POST /api/verify-otp
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        if (!OtpService::verifierCode($validated['email'], $validated['code'], 'admin', 'connexion')) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        $user->load(['fonctions', 'sousfonctions']);

        return response()->json($this->emettreSession($user));
    }

    /**
     * Renvoie un nouveau code OTP de connexion.
     *
     * POST /api/resend-otp
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $code = OtpService::genererCode($user->email, 'admin', 'connexion');

            try {
                Mail::to($user->email)->send(new OtpCodeMail($user->nom_complet ?? $user->username, $code, 'connexion'));
            } catch (\Exception $e) {
                Log::warning('Echec renvoi email OTP (admin)', ['error' => $e->getMessage()]);
            }
        }

        return response()->json(['message' => 'Si ce compte existe, un nouveau code vient de vous être envoyé.']);
    }

    /**
     * Supprime les anciens tokens et en délivre un nouveau — factorisé car
     * appelé aussi bien par login() (OTP désactivé) que par verifyOtp()
     * (OTP activé, une fois le code validé).
     */
    private function emettreSession(User $user): array
    {
        $user->tokens()->delete();

        $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
            'expires_at' => now()->addDays(7)->toIso8601String(),
            'message' => 'Connexion réussie'
        ];
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Utilisateur connecté
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['fonctions', 'sousfonctions']);
        return response()->json($user);
    }

    /**
     * Inscription (optionnel)
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users',
            'nom_complet' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Inscription réussie'
        ], 201);
    }

    /**
     * Demande de réinitialisation de mot de passe.
     *
     * Répond toujours avec succès, que l'email existe ou non, pour ne pas
     * révéler quels emails sont enregistrés.
     *
     * POST /api/forgot-password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $token = PasswordResetService::creerToken($user->email, 'admin');
            $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email) . '&type=admin';

            if (!app()->isProduction()) {
                Log::info("Réinitialisation mot de passe [admin] pour {$user->email} : {$resetUrl}");
            }

            try {
                Mail::to($user->email)->send(new ResetPasswordMail($user->nom_complet ?? $user->username, $resetUrl));
            } catch (\Exception $e) {
                Log::warning('Echec envoi email de réinitialisation (admin)', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'message' => 'Si cet email existe, un lien de réinitialisation vient de vous être envoyé.'
        ]);
    }

    /**
     * Réinitialisation effective du mot de passe via le token reçu par email.
     *
     * POST /api/reset-password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!PasswordResetService::verifierToken($validated['email'], $validated['token'], 'admin')) {
            return response()->json(['message' => 'Ce lien de réinitialisation est invalide ou a expiré.'], 422);
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'Ce lien de réinitialisation est invalide ou a expiré.'], 422);
        }

        $user->update(['password' => Hash::make($validated['password'])]);
        PasswordResetService::oublierToken($validated['email'], 'admin');

        // Un changement de mot de passe invalide les sessions existantes
        $user->tokens()->delete();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès.']);
    }
}
