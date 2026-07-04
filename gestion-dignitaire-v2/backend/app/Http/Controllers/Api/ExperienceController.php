<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExperienceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('experiences as e')
            ->select([
                'e.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                's.nom as structure_nom'
            ])
            ->leftJoin('dignitaire as d', 'e.dignitaire_id', '=', 'd.id')
            ->leftJoin('structure as s', 'e.structure_id', '=', 's.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('e.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('e.intitule', 'like', "%{$search}%")
                  ->orWhere('s.nom', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(d.prenom, ' ', d.nom)"), 'like', "%{$search}%");
            });
        }

        $experiences = $query->orderBy('e.date_debut', 'desc')->get();

        return response()->json($experiences);
    }

    public function show(int $id): JsonResponse
    {
        $experience = DB::table('experiences as e')
            ->select([
                'e.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                's.nom as structure_nom'
            ])
            ->leftJoin('dignitaire as d', 'e.dignitaire_id', '=', 'd.id')
            ->leftJoin('structure as s', 'e.structure_id', '=', 's.id')
            ->where('e.id', $id)
            ->first();

        if (!$experience) {
            return response()->json(['message' => 'Expérience non trouvée'], 404);
        }

        return response()->json($experience);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'structure_id' => 'nullable|exists:structure,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        $id = DB::table('experiences')->insertGetId($validated);

        AuditLogger::log($request, 'created', 'Experience', $id, $validated['intitule'] ?? null, null, $validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'structure_id' => 'nullable|exists:structure,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        $old = (array) DB::table('experiences')->where('id', $id)->first();
        DB::table('experiences')->where('id', $id)->update($validated);

        AuditLogger::log($request, 'updated', 'Experience', $id, $validated['intitule'] ?? null, $old, $validated);

        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $old = (array) DB::table('experiences')->where('id', $id)->first();
        DB::table('experiences')->where('id', $id)->delete();

        AuditLogger::log($request, 'deleted', 'Experience', $id, $old['intitule'] ?? null, $old, null);

        return response()->json(['message' => 'Expérience supprimée avec succès']);
    }
}
