<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NominationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('nominations as n')
            ->select([
                'n.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'p.intitule as poste_nom'
            ])
            ->leftJoin('dignitaire as d', 'n.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'n.entite_id', '=', 'e.id')
            ->leftJoin('postes as p', 'n.poste_id', '=', 'p.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('n.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('entite_id') && $request->entite_id) {
            $query->where('n.entite_id', $request->entite_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('n.fonction', 'like', "%{$search}%")
                  ->orWhere('e.nom', 'like', "%{$search}%")
                  ->orWhere('p.intitule', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(d.prenom, ' ', d.nom)"), 'like', "%{$search}%");
            });
        }

        $nominations = $query->orderBy('n.date_debut', 'desc')->get();

        return response()->json($nominations);
    }

    public function show(int $id): JsonResponse
    {
        $nomination = DB::table('nominations as n')
            ->select([
                'n.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'p.intitule as poste_nom'
            ])
            ->leftJoin('dignitaire as d', 'n.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'n.entite_id', '=', 'e.id')
            ->leftJoin('postes as p', 'n.poste_id', '=', 'p.id')
            ->where('n.id', $id)
            ->first();

        if (!$nomination) {
            return response()->json(['message' => 'Nomination non trouvée'], 404);
        }

        return response()->json($nomination);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'entite_id' => 'nullable|exists:entite,id',
            'poste_id' => 'nullable|exists:postes,id',
            'fonction' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'numero_decret' => 'nullable|string|max:100',
        ]);

        $id = DB::table('nominations')->insertGetId($validated);
        
        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'entite_id' => 'nullable|exists:entite,id',
            'poste_id' => 'nullable|exists:postes,id',
            'fonction' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'numero_decret' => 'nullable|string|max:100',
        ]);

        DB::table('nominations')->where('id', $id)->update($validated);
        
        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('nominations')->where('id', $id)->delete();
        return response()->json(['message' => 'Nomination supprimée avec succès']);
    }
}
