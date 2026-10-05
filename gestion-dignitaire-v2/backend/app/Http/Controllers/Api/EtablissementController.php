<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $validated = $this->validateData($request);

        $etablissement = Etablissement::whereRaw('LOWER(nom) = ?', [mb_strtolower($validated['nom'])])->first();

        if (!$etablissement) {
            $etablissement = Etablissement::create($validated);
            AuditLogger::log($request, 'created', 'Etablissement', $etablissement->id, $etablissement->nom, null, $validated);
            return response()->json($etablissement->load('ville'), 201);
        }

        return response()->json($etablissement->load('ville'));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Etablissement::with('ville')->findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $etablissement = Etablissement::findOrFail($id);
        $validated = $this->validateData($request);
        $duplicate = Etablissement::whereRaw('LOWER(nom) = ?', [mb_strtolower(trim($validated['nom']))])
            ->where('id', '!=', $id)
            ->exists();
        if ($duplicate) {
            abort(422, 'Un établissement portant ce nom existe déjà.');
        }

        $old = $etablissement->getOriginal();
        $etablissement->update($validated);
        AuditLogger::log($request, 'updated', 'Etablissement', $id, $etablissement->nom, $old, $validated);

        return response()->json($etablissement->fresh()->load('ville'));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $etablissement = Etablissement::findOrFail($id);
        $usages = DB::table('diplome')->where('etablissement_id', $id)->count();
        if ($usages > 0) {
            return response()->json([
                'message' => "Cet établissement est utilisé par {$usages} diplôme(s) et ne peut pas être supprimé.",
                'usages' => $usages,
            ], 409);
        }

        $old = $etablissement->getOriginal();
        $label = $etablissement->nom;
        $etablissement->delete();
        AuditLogger::log($request, 'deleted', 'Etablissement', $id, $label, $old, null);

        return response()->json(['message' => 'Établissement supprimé avec succès.']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nom' => 'required|string|max:150',
            'type' => 'nullable|string|max:50',
            'ville_id' => 'nullable|exists:ville,id',
        ]);
    }
}
