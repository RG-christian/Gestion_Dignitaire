<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conjoint;
use App\Models\Dignitaire;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur de gestion des conjoints des dignitaires
 */
class ConjointController extends Controller
{
    /**
     * Liste des conjoints d'un dignitaire
     *
     * GET /api/dignitaires/{dignitaireId}/conjoints
     */
    public function index(int $dignitaireId): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($dignitaireId);

        $conjoints = $dignitaire->conjoints()
            ->with(['lieuNaissance', 'nationalite'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'conjoints' => $conjoints
        ]);
    }

    /**
     * Liste globale des conjoints, tous dignitaires confondus, avec filtres
     * (page d'administration "Gestion du personnel" > Conjoints).
     *
     * GET /api/conjoints
     */
    public function indexAll(Request $request): JsonResponse
    {
        $query = Conjoint::query()
            ->select([
                'conjoints.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom_complet"),
            ])
            ->leftJoin('dignitaire as d', 'conjoints.dignitaire_id', '=', 'd.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('conjoints.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('conjoints.nom', 'like', "%{$search}%")
                  ->orWhere('conjoints.prenom', 'like', "%{$search}%");
            });
        }

        if ($request->has('statut') && $request->statut) {
            $query->where('conjoints.statut', $request->statut);
        }

        $conjoints = $query->orderBy('conjoints.nom')->get();

        return response()->json([
            'success' => true,
            'conjoints' => $conjoints,
        ]);
    }

    /**
     * Ajouter un conjoint
     * 
     * POST /api/dignitaires/{dignitaireId}/conjoints
     */
    public function store(Request $request, int $dignitaireId): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($dignitaireId);

        $validated = $request->validate([
            // Obligatoires
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'genre' => 'required|in:M,F',
            
            // Optionnels
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance_id' => 'nullable|exists:ville,id',
            'nationalite_id' => 'nullable|exists:pays,id',
            'profession' => 'nullable|string|max:255',
            'employeur' => 'nullable|string|max:255',
            'date_mariage' => 'nullable|date',
            'lieu_mariage' => 'nullable|string|max:255',
            'statut' => 'nullable|in:actif,divorce,veuf,separe',
            'date_fin_union' => 'nullable|date|after:date_mariage',
            
            // Statut spécial
            'est_militaire' => 'nullable|boolean',
            'est_dignitaire' => 'nullable|boolean',
            'grade_militaire' => 'nullable|string|max:100',
            'fonction_dignitaire' => 'nullable|string|max:100',
            
            // Coordonnées
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'adresse' => 'nullable|string',
            
            // Documents
            'photo' => 'nullable|string',
            'acte_mariage_path' => 'nullable|string',
        ]);

        try {
            $validated['dignitaire_id'] = $dignitaireId;
            $validated['statut'] = $validated['statut'] ?? 'actif';
            $validated['est_militaire'] = $validated['est_militaire'] ?? false;
            $validated['est_dignitaire'] = $validated['est_dignitaire'] ?? false;

            $conjoint = Conjoint::create($validated);

            AuditLogger::log($request, 'created', 'Conjoint', $conjoint->id, "{$conjoint->prenom} {$conjoint->nom}", null, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Conjoint ajouté avec succès',
                'conjoint' => $conjoint->load(['lieuNaissance', 'nationalite'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du conjoint',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détail d'un conjoint
     * 
     * GET /api/conjoints/{id}
     */
    public function show(int $id): JsonResponse
    {
        $conjoint = Conjoint::with(['dignitaire', 'lieuNaissance', 'nationalite'])
                            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'conjoint' => $conjoint
        ]);
    }

    /**
     * Modifier un conjoint
     * 
     * PUT /api/conjoints/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $conjoint = Conjoint::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'genre' => 'sometimes|in:M,F',
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance_id' => 'nullable|exists:ville,id',
            'nationalite_id' => 'nullable|exists:pays,id',
            'profession' => 'nullable|string|max:255',
            'employeur' => 'nullable|string|max:255',
            'date_mariage' => 'nullable|date',
            'lieu_mariage' => 'nullable|string|max:255',
            'statut' => 'sometimes|in:actif,divorce,veuf,separe',
            'date_fin_union' => 'nullable|date',
            'est_militaire' => 'nullable|boolean',
            'est_dignitaire' => 'nullable|boolean',
            'grade_militaire' => 'nullable|string|max:100',
            'fonction_dignitaire' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'adresse' => 'nullable|string',
            'photo' => 'nullable|string',
            'acte_mariage_path' => 'nullable|string',
        ]);

        try {
            $old = $conjoint->getOriginal();
            $conjoint->update($validated);

            AuditLogger::log($request, 'updated', 'Conjoint', $conjoint->id, "{$conjoint->prenom} {$conjoint->nom}", $old, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Conjoint modifié avec succès',
                'conjoint' => $conjoint->fresh()->load(['lieuNaissance', 'nationalite'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un conjoint
     * 
     * DELETE /api/conjoints/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $conjoint = Conjoint::findOrFail($id);

        try {
            $old = $conjoint->getOriginal();
            $label = "{$conjoint->prenom} {$conjoint->nom}";
            $conjoint->delete();

            AuditLogger::log($request, 'deleted', 'Conjoint', $id, $label, $old, null);

            return response()->json([
                'success' => true,
                'message' => 'Conjoint supprimé avec succès'
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
     * Marquer une union comme terminée
     * 
     * POST /api/conjoints/{id}/terminer-union
     */
    public function terminerUnion(Request $request, int $id): JsonResponse
    {
        $conjoint = Conjoint::findOrFail($id);

        $request->validate([
            'nouveau_statut' => 'required|in:divorce,veuf,separe',
            'date_fin' => 'nullable|date'
        ]);

        try {
            $old = $conjoint->getOriginal();
            $dateFin = $request->date_fin ? new \DateTime($request->date_fin) : null;
            $conjoint->terminerUnion($request->nouveau_statut, $dateFin);

            AuditLogger::log($request, 'updated', 'Conjoint', $conjoint->id, "{$conjoint->prenom} {$conjoint->nom}", $old, ['statut' => $request->nouveau_statut, 'date_fin_union' => $request->date_fin]);

            return response()->json([
                'success' => true,
                'message' => 'Union terminée avec succès',
                'conjoint' => $conjoint->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
