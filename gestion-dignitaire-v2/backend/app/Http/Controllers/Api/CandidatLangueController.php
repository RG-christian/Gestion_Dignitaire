<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidatLangue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur de gestion des langues déclarées par le candidat connecté
 */
class CandidatLangueController extends Controller
{
    /**
     * GET /api/candidats/me/langues
     */
    public function index(Request $request): JsonResponse
    {
        $candidat = $request->user();
        $langues = $candidat->langues()->with('langue')->get();

        return response()->json([
            'success' => true,
            'langues' => $langues,
        ]);
    }

    /**
     * POST /api/candidats/me/langues
     */
    public function store(Request $request): JsonResponse
    {
        $candidat = $request->user();

        if (!$candidat->estModifiable()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez plus modifier votre candidature.'
            ], 403);
        }

        $request->validate([
            'langue_id' => 'required|exists:langue,id',
            'niveau' => 'nullable|string|max:30',
        ]);

        $langue = $candidat->langues()->create([
            'langue_id' => $request->langue_id,
            'niveau' => $request->niveau,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Langue ajoutée avec succès',
            'langue' => $langue->load('langue'),
        ], 201);
    }

    /**
     * DELETE /api/candidats/me/langues/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $candidat = $request->user();

        if (!$candidat->estModifiable()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez plus modifier votre candidature.'
            ], 403);
        }

        $langue = CandidatLangue::where('candidat_id', $candidat->id)->findOrFail($id);
        $langue->delete();

        return response()->json([
            'success' => true,
            'message' => 'Langue supprimée avec succès',
        ]);
    }
}
