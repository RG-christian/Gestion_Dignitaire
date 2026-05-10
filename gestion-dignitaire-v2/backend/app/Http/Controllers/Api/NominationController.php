<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nomination;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NominationController extends Controller
{
    /**
     * Liste des nominations
     */
    public function index(Request $request): JsonResponse
    {
        $query = Nomination::with(['dignitaire', 'entite', 'poste', 'pv']);

        // Filtre par dignitaire
        if ($request->has('dignitaire_id')) {
            $query->where('dignitaire_id', $request->dignitaire_id);
        }

        // Filtre par entité
        if ($request->has('entite_id')) {
            $query->where('entite_id', $request->entite_id);
        }

        // Filtre nominations actives
        if ($request->has('actives') && $request->actives) {
            $query->actives();
        }

        $nominations = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json($nominations);
    }

    /**
     * Détails d'une nomination
     */
    public function show(int $id): JsonResponse
    {
        $nomination = Nomination::with(['dignitaire', 'entite', 'poste', 'pv'])
            ->findOrFail($id);

        return response()->json($nomination);
    }

    /**
     * Créer une nomination
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaires,id',
            'entite_id' => 'nullable|exists:entites,id',
            'poste_id' => 'nullable|exists:postes,id',
            'pv_id' => 'nullable|exists:pvs,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'fonction' => 'nullable|string|max:150',
        ]);

        $nomination = Nomination::create($validated);
        $nomination->load(['dignitaire', 'entite', 'poste', 'pv']);

        return response()->json($nomination, 201);
    }

    /**
     * Mettre à jour une nomination
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $nomination = Nomination::findOrFail($id);

        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaires,id',
            'entite_id' => 'nullable|exists:entites,id',
            'poste_id' => 'nullable|exists:postes,id',
            'pv_id' => 'nullable|exists:pvs,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'fonction' => 'nullable|string|max:150',
        ]);

        $nomination->update($validated);
        $nomination->load(['dignitaire', 'entite', 'poste', 'pv']);

        return response()->json($nomination);
    }

    /**
     * Supprimer une nomination
     */
    public function destroy(int $id): JsonResponse
    {
        $nomination = Nomination::findOrFail($id);
        $nomination->delete();

        return response()->json(['message' => 'Nomination supprimée avec succès']);
    }
}
