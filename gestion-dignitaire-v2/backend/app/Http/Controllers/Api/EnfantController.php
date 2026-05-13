<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enfant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EnfantController extends Controller
{
    /**
     * Liste des enfants avec requête optimisée
     */
    public function index(Request $request): JsonResponse
    {
        $query = Enfant::query()
            ->select([
                'enfants.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom_complet"),
                'v.nom as lieu_naissance_nom'
            ])
            ->leftJoin('dignitaire as d', 'enfants.dignitaire_id', '=', 'd.id')
            ->leftJoin('ville as v', 'enfants.lieu_naissance', '=', 'v.id');

        // Filtre par dignitaire
        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('enfants.dignitaire_id', $request->dignitaire_id);
        }

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('enfants.nom', 'like', "%{$search}%")
                  ->orWhere('enfants.prenom', 'like', "%{$search}%");
            });
        }

        $enfants = $query->orderBy('enfants.nom')->get();

        return response()->json($enfants);
    }

    /**
     * Détails d'un enfant
     */
    public function show(int $id): JsonResponse
    {
        $enfant = Enfant::with(['dignitaire', 'lieuNaissance.pays'])
            ->findOrFail($id);

        return response()->json($enfant);
    }

    /**
     * Créer un enfant
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'nullable|exists:ville,id',
            'genre' => 'required|in:M,F,Homme,Femme',
        ]);

        $enfant = Enfant::create($validated);

        return response()->json($enfant, 201);
    }

    /**
     * Mettre à jour un enfant
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $enfant = Enfant::findOrFail($id);

        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'nullable|exists:ville,id',
            'genre' => 'required|in:M,F,Homme,Femme',
        ]);

        $enfant->update($validated);

        return response()->json($enfant);
    }

    /**
     * Supprimer un enfant
     */
    public function destroy(int $id): JsonResponse
    {
        $enfant = Enfant::findOrFail($id);
        $enfant->delete();

        return response()->json(['message' => 'Enfant supprimé avec succès']);
    }
}
