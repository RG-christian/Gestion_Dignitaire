<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diplome;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DiplomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('diplome as d')
            ->select([
                'd.*',
                DB::raw("CONCAT(dig.prenom, ' ', dig.nom) as dignitaire_nom"),
                'etab.nom as etablissement_nom',
                'dom.nom as domaine_nom',
                'v.nom as ville_nom'
            ])
            ->leftJoin('dignitaire as dig', 'd.dignitaire_id', '=', 'dig.id')
            ->leftJoin('etablissement as etab', 'd.etablissement_id', '=', 'etab.id')
            ->leftJoin('domaine as dom', 'd.domaine_id', '=', 'dom.id')
            ->leftJoin('ville as v', 'd.ville_id', '=', 'v.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('d.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('d.intitule', 'like', "%{$search}%")
                  ->orWhere('etab.nom', 'like', "%{$search}%")
                  ->orWhere('d.annee', 'like', "%{$search}%");
            });
        }

        $diplomes = $query->orderBy('d.annee', 'desc')->get();

        return response()->json($diplomes);
    }

    public function show(int $id): JsonResponse
    {
        $diplome = Diplome::with(['dignitaire', 'etablissement', 'domaine', 'ville'])->findOrFail($id);
        return response()->json($diplome);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'etablissement_id' => 'nullable|exists:etablissement,id',
            'annee' => 'nullable|string|max:10',
            'ville_id' => 'nullable|exists:ville,id',
            'domaine_id' => 'nullable|exists:domaine,id',
            'code' => 'nullable|string|max:30',
            'type' => 'nullable|string|max:30',
        ]);

        $diplome = Diplome::create($validated);
        AuditLogger::log($request, 'created', 'Diplome', $diplome->id, $diplome->intitule, null, $validated);
        return response()->json($diplome, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $diplome = Diplome::findOrFail($id);

        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'etablissement_id' => 'nullable|exists:etablissement,id',
            'annee' => 'nullable|string|max:10',
            'ville_id' => 'nullable|exists:ville,id',
            'domaine_id' => 'nullable|exists:domaine,id',
            'code' => 'nullable|string|max:30',
            'type' => 'nullable|string|max:30',
        ]);

        $old = $diplome->getOriginal();
        $diplome->update($validated);
        AuditLogger::log($request, 'updated', 'Diplome', $diplome->id, $diplome->intitule, $old, $validated);
        return response()->json($diplome);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $diplome = Diplome::findOrFail($id);
        $old = $diplome->getOriginal();
        $label = $diplome->intitule;
        $diplome->delete();
        AuditLogger::log($request, 'deleted', 'Diplome', $id, $label, $old, null);
        return response()->json(['message' => 'Diplôme supprimé avec succès']);
    }
}
