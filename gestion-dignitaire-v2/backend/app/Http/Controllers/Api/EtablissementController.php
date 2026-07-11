<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Création à la volée d'un établissement (depuis le champ "recherche ou
 * ajout" utilisé dans les formulaires de diplôme). La lecture de la liste
 * reste gérée par ReferentielController::etablissements().
 */
class EtablissementController extends Controller
{
    /**
     * POST /api/etablissements
     *
     * Find-or-create : si un établissement du même nom existe déjà
     * (insensible à la casse), le renvoie tel quel plutôt que d'en créer
     * un doublon.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'ville_id' => 'nullable|exists:ville,id',
        ]);

        $etablissement = Etablissement::whereRaw('LOWER(nom) = ?', [mb_strtolower($validated['nom'])])->first();

        if (!$etablissement) {
            $etablissement = Etablissement::create($validated);
        }

        return response()->json(['id' => $etablissement->id, 'nom' => $etablissement->nom], 201);
    }
}
