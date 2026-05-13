<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaysController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('pays as p')
            ->select([
                'p.*',
                'r.nom as region_nom'
            ])
            ->leftJoin('region as r', 'p.region_id', '=', 'r.id');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('p.nom', 'like', "%{$search}%")
                  ->orWhere('p.code_iso', 'like', "%{$search}%")
                  ->orWhere('p.continent', 'like', "%{$search}%");
            });
        }

        $pays = $query->orderBy('p.continent')
                      ->orderBy('r.nom')
                      ->orderBy('p.nom')
                      ->get();

        return response()->json($pays);
    }

    public function show(int $id): JsonResponse
    {
        $pays = DB::table('pays as p')
            ->select([
                'p.*',
                'r.nom as region_nom'
            ])
            ->leftJoin('region as r', 'p.region_id', '=', 'r.id')
            ->where('p.id', $id)
            ->first();

        if (!$pays) {
            return response()->json(['message' => 'Pays non trouvé'], 404);
        }

        return response()->json($pays);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code_iso' => 'nullable|string|max:10',
            'indicatif' => 'nullable|string|max:10',
            'continent' => 'nullable|string|max:100',
            'region_id' => 'nullable|exists:region,id',
        ]);

        $id = DB::table('pays')->insertGetId($validated);
        
        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code_iso' => 'nullable|string|max:10',
            'indicatif' => 'nullable|string|max:10',
            'continent' => 'nullable|string|max:100',
            'region_id' => 'nullable|exists:region,id',
        ]);

        DB::table('pays')->where('id', $id)->update($validated);
        
        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('pays')->where('id', $id)->delete();
        return response()->json(['message' => 'Pays supprimé avec succès']);
    }
}
