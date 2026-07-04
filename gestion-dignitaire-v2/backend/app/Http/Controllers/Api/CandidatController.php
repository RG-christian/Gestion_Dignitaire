<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Models\Dignitaire;
use App\Models\Diplome;
use App\Models\LangueParlee;
use App\Models\Experience;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Contrôleur de gestion des candidats (Admin)
 * 
 * Permet aux administrateurs de consulter, valider ou refuser les candidatures
 */
class CandidatController extends Controller
{
    /**
     * Liste des candidats avec filtres
     * 
     * GET /api/admin/candidats
     */
    public function index(Request $request): JsonResponse
    {
        $query = Candidat::query();

        // Filtre par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par genre
        if ($request->has('genre')) {
            $query->byGenre($request->genre);
        }

        // Recherche
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Relations à charger
        $query->with(['lieuNaissance', 'villeResidence', 'validePar', 'dignitaire', 'documents']);

        // Tri
        $sortField = $request->get('sort_by', 'date_candidature');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $candidats = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'candidats' => $candidats
        ]);
    }

    /**
     * Détail d'un candidat
     * 
     * GET /api/admin/candidats/{id}
     */
    public function show(int $id): JsonResponse
    {
        $candidat = Candidat::with([
            'lieuNaissance',
            'villeResidence',
            'validePar',
            'dignitaire',
            'documents'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'candidat' => $candidat
        ]);
    }

    /**
     * Valider une candidature et créer le dignitaire
     * 
     * POST /api/admin/candidats/{id}/valider
     */
    public function valider(Request $request, int $id): JsonResponse
    {
        $candidat = Candidat::findOrFail($id);

        if ($candidat->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Cette candidature a déjà été traitée.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Le matricule est obligatoire côté dignitaire mais facultatif à la candidature
            // (un candidat externe n'en a pas forcément un au moment de son inscription) :
            // on en génère un provisoire si besoin, que l'admin pourra corriger ensuite.
            $matricule = $candidat->matricule ?: 'PROV' . str_pad($candidat->id, 6, '0', STR_PAD_LEFT);

            // Créer le dignitaire à partir du candidat
            $dignitaire = Dignitaire::create([
                'nip' => $candidat->nip,
                'matricule' => $matricule,
                'nom' => $candidat->nom,
                'prenom' => $candidat->prenom,
                'date_naissance' => $candidat->date_naissance,
                'lieu_naissance' => $candidat->lieu_naissance_id,
                'genre' => $candidat->genre,
                'etat_civil' => $candidat->etat_civil,
                'photo' => $candidat->photo,
                'email' => $candidat->email,
                'telephone_personnel' => $candidat->telephone,
                'adresse_personnelle' => $candidat->adresse,
            ]);

            // Recopier les diplômes, langues et expériences déclarés par le candidat
            foreach ($candidat->diplomes as $diplome) {
                Diplome::create([
                    'dignitaire_id' => $dignitaire->id,
                    'intitule' => $diplome->intitule,
                    'etablissement_id' => $diplome->etablissement_id,
                    'ville_id' => $diplome->ville_id,
                    'domaine_id' => $diplome->domaine_id,
                    'annee' => $diplome->annee,
                    'justificatif_path' => $diplome->justificatif_path,
                ]);
            }

            foreach ($candidat->langues as $langue) {
                LangueParlee::create([
                    'dignitaire_id' => $dignitaire->id,
                    'langue_id' => $langue->langue_id,
                    'niveau' => $langue->niveau,
                ]);
            }

            foreach ($candidat->experiences as $experience) {
                Experience::create([
                    'dignitaire_id' => $dignitaire->id,
                    'intitule' => $experience->intitule,
                    'structure_id' => $experience->structure_id,
                    'date_debut' => $experience->date_debut,
                    'date_fin' => $experience->date_fin,
                    'justificatif_path' => $experience->justificatif_path,
                ]);
            }

            // Marquer le candidat comme validé
            $candidat->valider((int) $request->user()->id, $dignitaire->id);

            AuditLogger::log($request, 'validated', 'Candidat', $candidat->id, "{$candidat->prenom} {$candidat->nom}", ['statut' => 'en_attente'], ['statut' => 'valide', 'dignitaire_id' => $dignitaire->id]);

            DB::commit();

            // TODO: Envoyer email de confirmation au candidat
            // Mail::to($candidat->email)->send(new CandidatureValidee($candidat));

            return response()->json([
                'success' => true,
                'message' => 'Candidature validée avec succès. Le dignitaire a été créé.',
                'candidat' => $candidat->fresh()->load('dignitaire'),
                'dignitaire' => $dignitaire
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refuser une candidature
     * 
     * POST /api/admin/candidats/{id}/refuser
     */
    public function refuser(Request $request, int $id): JsonResponse
    {
        $candidat = Candidat::findOrFail($id);

        if ($candidat->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Cette candidature a déjà été traitée.'
            ], 400);
        }

        $request->validate([
            'motif' => 'required|string|min:10'
        ]);

        try {
            $candidat->refuser((int) $request->user()->id, $request->motif);

            AuditLogger::log($request, 'refused', 'Candidat', $candidat->id, "{$candidat->prenom} {$candidat->nom}", ['statut' => 'en_attente'], ['statut' => 'refuse', 'motif_refus' => $request->motif]);

            // TODO: Envoyer email de refus au candidat
            // Mail::to($candidat->email)->send(new CandidatureRefusee($candidat));

            return response()->json([
                'success' => true,
                'message' => 'Candidature refusée',
                'candidat' => $candidat->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du refus de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un candidat
     * 
     * DELETE /api/admin/candidats/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $candidat = Candidat::findOrFail($id);

        try {
            // Supprimer la photo si elle existe
            if ($candidat->photo) {
                \Storage::disk('public')->delete($candidat->photo);
            }

            // Les documents seront supprimés automatiquement (cascade)
            $candidat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Candidat supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des candidatures
     * 
     * GET /api/admin/candidats/stats
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Candidat::count(),
            'en_attente' => Candidat::enAttente()->count(),
            'valides' => Candidat::valide()->count(),
            'refuses' => Candidat::refuse()->count(),
            'hommes' => Candidat::byGenre('M')->count(),
            'femmes' => Candidat::byGenre('F')->count(),
            'derniere_semaine' => Candidat::where('date_candidature', '>=', now()->subWeek())->count(),
            'ce_mois' => Candidat::whereMonth('date_candidature', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
