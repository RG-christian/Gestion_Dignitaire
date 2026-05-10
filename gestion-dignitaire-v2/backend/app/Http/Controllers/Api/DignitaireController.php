<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dignitaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DignitaireController extends Controller
{
    /**
     * Liste des dignitaires avec filtres et recherche
     */
    public function index(Request $request): JsonResponse
    {
        $query = Dignitaire::with([
            'lieuNaissance.pays',
            'postes.entite',
            'postes.ville'
        ]);

        // Recherche
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filtre par genre
        if ($request->has('genre')) {
            $query->byGenre($request->genre);
        }

        // Filtre par ville
        if ($request->has('ville_id')) {
            $query->byVille($request->ville_id);
        }

        // Filtre par entité (via postes)
        if ($request->has('entite_id')) {
            $query->whereHas('postes', function ($q) use ($request) {
                $q->where('entite_id', $request->entite_id);
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $dignitaires = $query->orderBy('id', 'desc')->paginate($perPage);

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
