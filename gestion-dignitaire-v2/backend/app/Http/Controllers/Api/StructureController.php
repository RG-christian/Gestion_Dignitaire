<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StructureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('structure');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%");
        }

        $structures = $query->orderBy('nom')->get();

        return response()->json($structures);
    }

    public function show(int $id): JsonResponse
    {
        $structure = DB::table('structure')->where('id', $id)->first();

        if (!$structure) {
            return response()->json(['message' => 'Structure non trouvée'], 404);
        }

        return response()->json($structure);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150|unique:structure,nom',
        ]);

        $id = DB::table('structure')->insertGetId($validated);
        
        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150|unique:structure,nom,' . $id,
        ]);

        DB::table('structure')->where('id', $id)->update($validated);
        
        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('structure')->where('id', $id)->delete();
        return response()->json(['message' => 'Structure supprimée avec succès']);
    }
}
