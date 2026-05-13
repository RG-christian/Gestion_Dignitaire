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
        $query = DB::table('region');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%");
        }

        $regions = $query->orderBy('nom')->get();

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
        ]);

        $id = DB::table('region')->insertGetId($validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
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
