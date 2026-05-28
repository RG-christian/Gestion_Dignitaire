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
        $perPage = $request->get('per_page', 20); // Pagination côté serveur
        $page = $request->get('page', 1);

        $query = DB::table('ville as v')
            ->select([
                'v.*',
                'p.nom as pays_nom',
                'p.code_iso as pays_code_iso',
                'r.nom as region_nom'
            ])
            ->leftJoin('pays as p', 'v.pays_id', '=', 'p.id')
            ->leftJoin('region as r', 'v.region_id', '=', 'r.id');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('v.nom', 'like', "%{$search}%")
                  ->orWhere('p.nom', 'like', "%{$search}%")
                  ->orWhere('r.nom', 'like', "%{$search}%");
            });
        }

        if ($request->has('pays_id') && $request->pays_id) {
            $query->where('v.pays_id', $request->pays_id);
        }

        // Compter le total avant pagination
        $total = $query->count();

        // Appliquer la pagination
        $villes = $query->orderBy('v.nom')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'data' => $villes,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $ville = DB::table('ville as v')
            ->select([
                'v.*',
                'p.nom as pays_nom',
                'p.code_iso as pays_code_iso',
                'r.nom as region_nom'
            ])
            ->leftJoin('pays as p', 'v.pays_id', '=', 'p.id')
            ->leftJoin('region as r', 'v.region_id', '=', 'r.id')
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
            'region_id' => 'nullable|exists:region,id',
        ]);

        $id = DB::table('ville')->insertGetId($validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'nullable|exists:pays,id',
            'region_id' => 'nullable|exists:region,id',
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
