<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('region as r')
            ->select([
                'r.*',
                DB::raw('(SELECT COUNT(*) FROM ville WHERE ville.region_id = r.id) as villes_count')
            ]);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('r.nom', 'like', "%{$search}%")
                  ->orWhere('r.continent', 'like', "%{$search}%")
                  ->orWhere('r.pays_nom', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('r.type', $request->type);
        }

        if ($request->has('continent') && $request->continent) {
            $query->where('r.continent', $request->continent);
        }

        $regions = $query->orderBy('r.nom')->get();

        return response()->json($regions);
    }

    public function show(int $id): JsonResponse
    {
        $region = DB::table('region')->where('id', $id)->first();

        if (!$region) {
            return response()->json(['message' => 'Région non trouvée'], 404);
        }

        return response()->json($region);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'continent' => 'nullable|string|max:100',
            'type' => 'required|in:region,province',
            'pays_nom' => 'nullable|string|max:100',
        ]);

        // Validation conditionnelle
        if ($validated['type'] === 'region' && empty($validated['continent'])) {
            return response()->json(['message' => 'Le continent est requis pour une région'], 422);
        }

        if ($validated['type'] === 'province' && empty($validated['pays_nom'])) {
            return response()->json(['message' => 'Le pays est requis pour une province'], 422);
        }

        $id = DB::table('region')->insertGetId($validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'continent' => 'nullable|string|max:100',
            'type' => 'required|in:region,province',
            'pays_nom' => 'nullable|string|max:100',
        ]);

        DB::table('region')->where('id', $id)->update($validated);

        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('region')->where('id', $id)->delete();
        return response()->json(['message' => 'Région supprimée avec succès']);
    }
}
