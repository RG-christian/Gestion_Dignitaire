<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VilleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('ville as v')
            ->select([
                'v.*',
                'p.nom as pays_nom'
            ])
            ->leftJoin('pays as p', 'v.pays_id', '=', 'p.id');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('v.nom', 'like', "%{$search}%")
                  ->orWhere('p.nom', 'like', "%{$search}%");
            });
        }

        if ($request->has('pays_id') && $request->pays_id) {
            $query->where('v.pays_id', $request->pays_id);
        }

        $villes = $query->orderBy('v.nom')->get();

        return response()->json($villes);
    }

    public function show(int $id): JsonResponse
    {
        $ville = DB::table('ville as v')
            ->select([
                'v.*',
                'p.nom as pays_nom'
            ])
            ->leftJoin('pays as p', 'v.pays_id', '=', 'p.id')
            ->where('v.id', $id)
            ->first();

        if (!$ville) {
            return response()->json(['message' => 'Ville non trouvée'], 404);
        }

        return response()->json($ville);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'nullable|exists:pays,id',
        ]);

        $id = DB::table('ville')->insertGetId($validated);
        
        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'nullable|exists:pays,id',
        ]);

        DB::table('ville')->where('id', $id)->update($validated);
        
        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('ville')->where('id', $id)->delete();
        return response()->json(['message' => 'Ville supprimée avec succès']);
    }
}
