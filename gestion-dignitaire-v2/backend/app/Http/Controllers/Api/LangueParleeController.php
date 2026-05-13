<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LangueParleeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('langues as lp')
            ->select([
                'lp.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'l.nom as langue_nom'
            ])
            ->leftJoin('dignitaire as d', 'lp.dignitaire_id', '=', 'd.id')
            ->leftJoin('langue as l', 'lp.langue_id', '=', 'l.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('lp.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('l.nom', 'like', "%{$search}%")
                  ->orWhere('lp.niveau', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(d.prenom, ' ', d.nom)"), 'like', "%{$search}%");
            });
        }

        $languesParlees = $query->orderBy('d.nom')->get();

        return response()->json($languesParlees);
    }

    public function show(int $id): JsonResponse
    {
        $langueParlee = DB::table('langues as lp')
            ->select([
                'lp.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'l.nom as langue_nom'
            ])
            ->leftJoin('dignitaire as d', 'lp.dignitaire_id', '=', 'd.id')
            ->leftJoin('langue as l', 'lp.langue_id', '=', 'l.id')
            ->where('lp.id', $id)
            ->first();

        if (!$langueParlee) {
            return response()->json(['message' => 'Langue parlée non trouvée'], 404);
        }

        return response()->json($langueParlee);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'langue_id' => 'required|exists:langue,id',
            'niveau' => 'nullable|string|max:50',
        ]);

        $id = DB::table('langues')->insertGetId($validated);
        
        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'langue_id' => 'required|exists:langue,id',
            'niveau' => 'nullable|string|max:50',
        ]);

        DB::table('langues')->where('id', $id)->update($validated);
        
        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('langues')->where('id', $id)->delete();
        return response()->json(['message' => 'Langue parlée supprimée avec succès']);
    }
}
