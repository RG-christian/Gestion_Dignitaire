<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidatDiplome;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur de gestion des diplômes déclarés par le candidat connecté
 */
class CandidatDiplomeController extends Controller
{
    /**
     * GET /api/candidats/me/diplomes
     */
    public function index(Request $request): JsonResponse
    {
        $candidat = $request->user();
        $diplomes = $candidat->diplomes()->with(['etablissement', 'ville', 'domaine'])->latest()->get();

        return response()->json([
            'success' => true,
            'diplomes' => $diplomes,
        ]);
    }

    /**
     * POST /api/candidats/me/diplomes
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
            'intitule' => 'required|string|max:255',
            'etablissement_id' => 'nullable|exists:etablissement,id',
            'ville_id' => 'nullable|exists:ville,id',
            'domaine_id' => 'nullable|exists:domaine,id',
            'annee' => 'nullable|string|max:10',
            'justificatif' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $path = null;
        if ($request->hasFile('justificatif')) {
            $path = $request->file('justificatif')->store('candidats/diplomes', 'public');
        }

        $diplome = $candidat->diplomes()->create([
            'intitule' => $request->intitule,
            'etablissement_id' => $request->etablissement_id,
            'ville_id' => $request->ville_id,
            'domaine_id' => $request->domaine_id,
            'annee' => $request->annee,
            'justificatif_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Diplôme ajouté avec succès',
            'diplome' => $diplome,
        ], 201);
    }

    /**
     * PUT /api/candidats/me/diplomes/{id}
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

        $diplome = CandidatDiplome::where('candidat_id', $candidat->id)->findOrFail($id);

        $request->validate([
            'intitule' => 'required|string|max:255',
            'etablissement_id' => 'nullable|exists:etablissement,id',
            'ville_id' => 'nullable|exists:ville,id',
            'domaine_id' => 'nullable|exists:domaine,id',
            'annee' => 'nullable|string|max:10',
            'justificatif' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($request->hasFile('justificatif')) {
            if ($diplome->justificatif_path) {
                \Storage::disk('public')->delete($diplome->justificatif_path);
            }
            $diplome->justificatif_path = $request->file('justificatif')->store('candidats/diplomes', 'public');
        }

        $diplome->fill($request->only(['intitule', 'etablissement_id', 'ville_id', 'domaine_id', 'annee']));
        $diplome->save();

        return response()->json([
            'success' => true,
            'message' => 'Diplôme mis à jour avec succès',
            'diplome' => $diplome,
        ]);
    }

    /**
     * DELETE /api/candidats/me/diplomes/{id}
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

        $diplome = CandidatDiplome::where('candidat_id', $candidat->id)->findOrFail($id);
        $diplome->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diplôme supprimé avec succès',
        ]);
    }
}
