<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PosteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('postes as p')
            ->select([
                'p.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'v.nom as ville_nom'
            ])
            ->leftJoin('dignitaire as d', 'p.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'p.entite_id', '=', 'e.id')
            ->leftJoin('ville as v', 'p.ville_id', '=', 'v.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('p.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('p.intitule', 'like', "%{$search}%")
                  ->orWhere('e.nom', 'like', "%{$search}%")
                  ->orWhere('v.nom', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(d.prenom, ' ', d.nom)"), 'like', "%{$search}%");
            });
        }

        $postes = $query->orderBy('p.date_debut', 'desc')->get();

        return response()->json($postes);
    }

    public function show(int $id): JsonResponse
    {
        $poste = DB::table('postes as p')
            ->select([
                'p.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'v.nom as ville_nom'
            ])
            ->leftJoin('dignitaire as d', 'p.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'p.entite_id', '=', 'e.id')
            ->leftJoin('ville as v', 'p.ville_id', '=', 'v.id')
            ->where('p.id', $id)
            ->first();

        if (!$poste) {
            return response()->json(['message' => 'Poste non trouvé'], 404);
        }

        return response()->json($poste);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'entite_id' => 'nullable|exists:entite,id',
            'ville_id' => 'nullable|exists:ville,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        $id = DB::table('postes')->insertGetId($validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'entite_id' => 'nullable|exists:entite,id',
            'ville_id' => 'nullable|exists:ville,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        DB::table('postes')->where('id', $id)->update($validated);

        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('postes')->where('id', $id)->delete();
        return response()->json(['message' => 'Poste supprimé avec succès']);
    }
}
