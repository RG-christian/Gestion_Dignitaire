<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dignitaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DignitaireController extends Controller
{
    /**
     * Liste des dignitaires avec filtres et recherche
     */
    public function index(Request $request): JsonResponse
    {
        $query = Dignitaire::query()
            ->select([
                'dignitaire.*',
                'ville.nom as lieu_naissance_nom',
                DB::raw('(SELECT intitule FROM postes WHERE postes.dignitaire_id = dignitaire.id AND (postes.date_fin IS NULL OR postes.date_fin >= NOW()) ORDER BY postes.date_debut DESC LIMIT 1) as poste_actuel'),
                DB::raw('(SELECT ville.nom FROM postes LEFT JOIN ville ON postes.ville_id = ville.id WHERE postes.dignitaire_id = dignitaire.id AND (postes.date_fin IS NULL OR postes.date_fin >= NOW()) ORDER BY postes.date_debut DESC LIMIT 1) as ville_poste'),
                DB::raw('(SELECT entite.nom FROM postes LEFT JOIN entite ON postes.entite_id = entite.id WHERE postes.dignitaire_id = dignitaire.id AND (postes.date_fin IS NULL OR postes.date_fin >= NOW()) ORDER BY postes.date_debut DESC LIMIT 1) as nom_entite')
            ])
            ->leftJoin('ville', 'dignitaire.lieu_naissance', '=', 'ville.id');

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dignitaire.nom', 'like', "%{$search}%")
                  ->orWhere('dignitaire.prenom', 'like', "%{$search}%")
                  ->orWhere('dignitaire.matricule', 'like', "%{$search}%")
                  ->orWhere('dignitaire.nip', 'like', "%{$search}%");
            });
        }

        // Filtre par genre
        if ($request->has('genre') && $request->genre) {
            $query->where('dignitaire.genre', $request->genre);
        }

        // Filtre par ville
        if ($request->has('ville_id') && $request->ville_id) {
            $query->where('dignitaire.lieu_naissance', $request->ville_id);
        }

        // Filtre par entité (via postes)
        if ($request->has('entite_id') && $request->entite_id) {
            $query->whereExists(function ($q) use ($request) {
                $q->select(DB::raw(1))
                  ->from('postes')
                  ->whereColumn('postes.dignitaire_id', 'dignitaire.id')
                  ->where('postes.entite_id', $request->entite_id);
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $dignitaires = $query->orderBy('dignitaire.id', 'desc')->paginate($perPage);

        return response()->json($dignitaires);
    }

    /**
     * Détails d'un dignitaire
     */
    public function show(int $id): JsonResponse
    {
        $dignitaire = Dignitaire::with([
            'lieuNaissance.pays',
            'diplomes.etablissement',
            'diplomes.domaine',
            'enfants.lieuNaissance',
            'languesParlees.langue',
            'experiences.structure',
            'postes.entite',
            'postes.ville',
            'nominations.entite',
            'nominations.poste',
            'decorations'
        ])->findOrFail($id);

        return response()->json($dignitaire);
    }

    /**
     * Créer un dignitaire
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:dignitaires',
            'matricule' => 'required|string|max:20|unique:dignitaires',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|exists:villes,id',
            'nationalite' => 'nullable|string|max:100',
            'genre' => 'nullable|in:Homme,Femme',
            'etat_civil' => 'nullable|string|max:20',
            'photo' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
        ]);

        $dignitaire = Dignitaire::create($validated);

        return response()->json($dignitaire, 201);
    }

    /**
     * Mettre à jour un dignitaire
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($id);

        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:dignitaires,nip,' . $id,
            'matricule' => 'required|string|max:20|unique:dignitaires,matricule,' . $id,
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|exists:villes,id',
            'nationalite' => 'nullable|string|max:100',
            'genre' => 'nullable|in:Homme,Femme',
            'etat_civil' => 'nullable|string|max:20',
            'photo' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
        ]);

        $dignitaire->update($validated);

        return response()->json($dignitaire);
    }

    /**
     * Supprimer un dignitaire
     */
    public function destroy(int $id): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($id);
        $dignitaire->delete();

        return response()->json(['message' => 'Dignitaire supprimé avec succès']);
    }

    /**
     * Statistiques du dashboard
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_dignitaires' => Dignitaire::count(),
            'total_postes' => \App\Models\Poste::count(),
            'total_decorations' => \App\Models\Decoration::count(),
            'total_villes' => \App\Models\Ville::count(),
            'par_genre' => Dignitaire::selectRaw('genre, COUNT(*) as count')
                ->groupBy('genre')
                ->get(),
        ]);
    }
}
