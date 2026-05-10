<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Decoration;
use App\Models\Dignitaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DecorationController extends Controller
{
    /**
     * Liste des décorations
     */
    public function index(Request $request): JsonResponse
    {
        $query = Decoration::with('dignitaires');

        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        $decorations = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json($decorations);
    }

    /**
     * Détails d'une décoration
     */
    public function show(int $id): JsonResponse
    {
        $decoration = Decoration::with('dignitaires')->findOrFail($id);

        return response()->json($decoration);
    }

    /**
     * Créer une décoration
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'type' => 'nullable|string|max:50',
            'niveau' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'date_obtention' => 'nullable|date',
            'autorite' => 'nullable|string|max:50',
            'motif' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'fichier_attestation' => 'nullable|string|max:100',
        ]);

        $decoration = Decoration::create($validated);

        return response()->json($decoration, 201);
    }

    /**
     * Mettre à jour une décoration
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $decoration = Decoration::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'type' => 'nullable|string|max:50',
            'niveau' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'date_obtention' => 'nullable|date',
            'autorite' => 'nullable|string|max:50',
            'motif' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'fichier_attestation' => 'nullable|string|max:100',
        ]);

        $decoration->update($validated);

        return response()->json($decoration);
    }

    /**
     * Supprimer une décoration
     */
    public function destroy(int $id): JsonResponse
    {
        $decoration = Decoration::findOrFail($id);
        $decoration->delete();

        return response()->json(['message' => 'Décoration supprimée avec succès']);
    }

    /**
     * Attribuer une décoration à un dignitaire
     */
    public function attachToDignitaire(Request $request, int $dignitaireId): JsonResponse
    {
        $validated = $request->validate([
            'decoration_id' => 'required|exists:decorations,id',
            'date_attribution' => 'required|date',
        ]);

        $dignitaire = Dignitaire::findOrFail($dignitaireId);
        
        $dignitaire->decorations()->attach($validated['decoration_id'], [
            'date_attribution' => $validated['date_attribution']
        ]);

        return response()->json([
            'message' => 'Décoration attribuée avec succès'
        ]);
    }

    /**
     * Retirer une décoration d'un dignitaire
     */
    public function detachFromDignitaire(int $dignitaireId, int $decorationId): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($dignitaireId);
        $dignitaire->decorations()->detach($decorationId);

        return response()->json([
            'message' => 'Décoration retirée avec succès'
        ]);
    }
}
