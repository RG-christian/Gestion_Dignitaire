<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Pays, Region, Ville, Entite, Langue, Domaine, Structure, Etablissement};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferentielController extends Controller
{
    /**
     * Regroupe plusieurs petites listes de référence dans une seule réponse.
     * Seules les clés explicitement demandées et autorisées sont retournées.
     */
    public function bundle(Request $request): JsonResponse
    {
        $request->validate(['include' => 'required|string|max:200']);

        $autorises = [
            'villes', 'pays', 'regions', 'langues', 'domaines',
            'structures', 'etablissements', 'entites', 'dignitaires',
        ];
        $demandes = array_values(array_unique(array_intersect(
            array_filter(explode(',', $request->string('include')->toString())),
            $autorises
        )));

        $data = [];
        foreach ($demandes as $cle) {
            $data[$cle] = match ($cle) {
                'villes' => DB::table('ville')
                    ->select(['id', 'nom', 'pays_id'])
                    ->orderBy('nom')
                    ->get(),
                'pays' => DB::table('pays')
                    ->select(['id', 'nom'])
                    ->orderBy('nom')
                    ->get(),
                'regions' => Region::orderBy('nom')->get(),
                'langues' => Langue::orderBy('nom')->get(),
                'domaines' => Domaine::orderBy('nom')->get(),
                'structures' => Structure::with('ville')->orderBy('nom')->get(),
                'etablissements' => Etablissement::with('ville')->orderBy('nom')->get(),
                'entites' => Entite::with(['parent', 'enfants', 'rattachement'])->orderBy('nom')->get(),
                'dignitaires' => DB::table('dignitaire')
                    ->select(['id', 'nom', 'prenom', 'matricule'])
                    ->orderBy('nom')
                    ->orderBy('prenom')
                    ->get(),
            };
        }

        return response()->json($data);
    }

    /**
     * Liste des pays
     */
    public function pays(Request $request): JsonResponse
    {
        $query = Pays::with('region');

        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        $pays = $query->orderBy('nom')->get();

        return response()->json($pays);
    }

    /**
     * Liste des régions
     */
    public function regions(): JsonResponse
    {
        $regions = Region::orderBy('nom')->get();

        return response()->json($regions);
    }

    /**
     * Liste des villes
     */
    public function villes(Request $request): JsonResponse
    {
        $query = Ville::with('pays');

        if ($request->has('pays_id')) {
            $query->where('pays_id', $request->pays_id);
        }

        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        $villes = $query->orderBy('nom')->get();

        return response()->json($villes);
    }

    /**
     * Liste des entités
     */
    public function entites(Request $request): JsonResponse
    {
        $query = Entite::with(['parent', 'enfants']);

        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        $entites = $query->orderBy('nom')->get();

        return response()->json($entites);
    }

    /**
     * Liste des langues
     */
    public function langues(): JsonResponse
    {
        $langues = Langue::orderBy('nom')->get();

        return response()->json($langues);
    }

    /**
     * Liste des domaines
     */
    public function domaines(): JsonResponse
    {
        $domaines = Domaine::orderBy('nom')->get();

        return response()->json($domaines);
    }

    /**
     * Liste des structures
     */
    public function structures(Request $request): JsonResponse
    {
        $query = Structure::with('ville');

        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        $structures = $query->orderBy('nom')->get();

        return response()->json($structures);
    }

    /**
     * Liste des établissements
     */
    public function etablissements(Request $request): JsonResponse
    {
        $query = Etablissement::with('ville');

        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        $etablissements = $query->orderBy('nom')->get();

        return response()->json($etablissements);
    }
}
