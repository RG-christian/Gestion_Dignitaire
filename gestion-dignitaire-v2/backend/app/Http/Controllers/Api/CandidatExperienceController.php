<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidatExperience;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur de gestion des expériences professionnelles déclarées par le candidat connecté
 */
class CandidatExperienceController extends Controller
{
    /**
     * GET /api/candidats/me/experiences
     */
    public function index(Request $request): JsonResponse
    {
        $candidat = $request->user();
        $experiences = $candidat->experiences()->with('structure')->orderByDesc('date_debut')->get();

        return response()->json([
            'success' => true,
            'experiences' => $experiences,
        ]);
    }

    /**
     * POST /api/candidats/me/experiences
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
            'intitule' => 'required|string|max:150',
            'structure_id' => 'nullable|exists:structure,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'justificatif' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $path = null;
        if ($request->hasFile('justificatif')) {
            $path = $request->file('justificatif')->store('candidats/experiences', 'public');
        }

        $experience = $candidat->experiences()->create([
            'intitule' => $request->intitule,
            'structure_id' => $request->structure_id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'justificatif_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expérience ajoutée avec succès',
            'experience' => $experience,
        ], 201);
    }

    /**
     * PUT /api/candidats/me/experiences/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $candidat = $request->user();

        if (!$candidat->estModifiable()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez plus modifier votre candidature.'
            ], 403);
        }

        $experience = CandidatExperience::where('candidat_id', $candidat->id)->findOrFail($id);

        $request->validate([
            'intitule' => 'required|string|max:150',
            'structure_id' => 'nullable|exists:structure,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'justificatif' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($request->hasFile('justificatif')) {
            if ($experience->justificatif_path) {
                \Storage::disk('public')->delete($experience->justificatif_path);
            }
            $experience->justificatif_path = $request->file('justificatif')->store('candidats/experiences', 'public');
        }

        $experience->fill($request->only(['intitule', 'structure_id', 'date_debut', 'date_fin']));
        $experience->save();

        return response()->json([
            'success' => true,
            'message' => 'Expérience mise à jour avec succès',
            'experience' => $experience,
        ]);
    }

    /**
     * DELETE /api/candidats/me/experiences/{id}
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

        $experience = CandidatExperience::where('candidat_id', $candidat->id)->findOrFail($id);
        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expérience supprimée avec succès',
        ]);
    }
}
