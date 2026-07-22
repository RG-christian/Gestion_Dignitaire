<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use App\Support\VilleResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Affectations à l'étranger (ambassade, mission, consulat...) d'un
 * dignitaire, distinctes de son poste (fonction/entité) et de sa
 * nationalité d'origine (CR de réunion, BLOC 9).
 */
class AffectationController extends Controller
{
    private function baseQuery(Request $request)
    {
        $query = DB::table('affectations as a')
            ->select([
                'a.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'p.nom as pays_nom',
                'v.nom as ville_nom',
            ])
            ->leftJoin('dignitaire as d', 'a.dignitaire_id', '=', 'd.id')
            ->leftJoin('pays as p', 'a.pays_id', '=', 'p.id')
            ->leftJoin('ville as v', 'a.ville_id', '=', 'v.id');

        if ($request->filled('dignitaire_id')) {
            $query->where('a.dignitaire_id', $request->dignitaire_id);
        }

        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        $affectations = $this->baseQuery($request)->orderBy('a.date_debut', 'desc')->get();

        return response()->json($affectations);
    }

    public function show(int $id): JsonResponse
    {
        $affectation = DB::table('affectations as a')
            ->select([
                'a.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'p.nom as pays_nom',
                'v.nom as ville_nom',
            ])
            ->leftJoin('dignitaire as d', 'a.dignitaire_id', '=', 'd.id')
            ->leftJoin('pays as p', 'a.pays_id', '=', 'p.id')
            ->leftJoin('ville as v', 'a.ville_id', '=', 'v.id')
            ->where('a.id', $id)
            ->first();

        if (!$affectation) {
            return response()->json(['message' => 'Affectation non trouvée'], 404);
        }

        return response()->json($affectation);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'pays_id' => 'required|exists:pays,id',
            'ville_id' => 'nullable|exists:ville,id',
            'ville_custom' => 'nullable|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'type_affectation' => 'nullable|string|max:100',
            'nature' => 'nullable|in:principale,mission_temporaire',
        ]);

        $validated['statut'] = 'en_cours';
        $validated['nature'] = $validated['nature'] ?? 'principale';
        $validated['ville_id'] = VilleResolver::resoudre($validated['ville_id'] ?? null, $validated['ville_custom'] ?? null, $validated['pays_id']);
        unset($validated['ville_custom']);

        $id = DB::table('affectations')->insertGetId($validated + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AuditLogger::log($request, 'created', 'Affectation', $id, null, null, $validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'pays_id' => 'required|exists:pays,id',
            'ville_id' => 'nullable|exists:ville,id',
            'ville_custom' => 'nullable|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'type_affectation' => 'nullable|string|max:100',
            'nature' => 'nullable|in:principale,mission_temporaire',
        ]);

        $validated['nature'] = $validated['nature'] ?? 'principale';
        $validated['ville_id'] = VilleResolver::resoudre($validated['ville_id'] ?? null, $validated['ville_custom'] ?? null, $validated['pays_id']);
        unset($validated['ville_custom']);

        $old = (array) DB::table('affectations')->where('id', $id)->first();
        DB::table('affectations')->where('id', $id)->update($validated + ['updated_at' => now()]);

        AuditLogger::log($request, 'updated', 'Affectation', $id, null, $old, $validated);

        return response()->json(['id' => $id, ...$validated]);
    }

    /**
     * Clôturer une affectation (fin de séjour).
     *
     * POST /api/affectations/{id}/cloturer
     */
    public function cloturer(Request $request, int $id): JsonResponse
    {
        $old = (array) DB::table('affectations')->where('id', $id)->first();

        if (!$old) {
            return response()->json(['message' => 'Affectation non trouvée'], 404);
        }

        $dateFinRule = 'nullable|date';
        if (!empty($old['date_debut'])) {
            $dateFinRule .= '|after:' . $old['date_debut'];
        }

        $validated = $request->validate([
            'date_fin' => $dateFinRule,
        ]);

        $update = [
            'statut' => 'terminee',
            'date_fin' => $validated['date_fin'] ?? now()->toDateString(),
            'updated_at' => now(),
        ];

        DB::table('affectations')->where('id', $id)->update($update);

        AuditLogger::log($request, 'cloturee', 'Affectation', $id, null, $old, $update);

        return response()->json(['id' => $id, ...$update]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $old = (array) DB::table('affectations')->where('id', $id)->first();
        DB::table('affectations')->where('id', $id)->delete();

        AuditLogger::log($request, 'deleted', 'Affectation', $id, null, $old, null);

        return response()->json(['message' => 'Affectation supprimée avec succès']);
    }
}
