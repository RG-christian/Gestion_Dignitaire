<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entite;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntiteController extends Controller
{
    /**
     * Liste toutes les entités avec leurs relations
     */
    public function index(Request $request): JsonResponse
    {
        $query = Entite::with(['parent', 'enfants']);

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $entites = $query->orderBy('nom')->get();

        return response()->json($entites);
    }

    /**
     * Affiche une entité spécifique
     */
    public function show($id): JsonResponse
    {
        $entite = Entite::with(['parent', 'enfants'])->find($id);

        if (!$entite) {
            return response()->json([
                'message' => 'Entité non trouvée'
            ], 404);
        }

        return response()->json($entite);
    }

    /**
     * Crée une nouvelle entité
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:150|unique:entite,nom',
            'type' => 'nullable|string|max:50',
            'id_sup' => 'nullable|exists:entite,id',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $entite = Entite::create($request->all());
        $entite->load(['parent', 'enfants']);

        AuditLogger::log($request, 'created', 'Entite', $entite->id, $entite->nom, null, $request->all());

        return response()->json($entite, 201);
    }

    /**
     * Met à jour une entité existante
     */
    public function update(Request $request, $id): JsonResponse
    {
        $entite = Entite::find($id);

        if (!$entite) {
            return response()->json([
                'message' => 'Entité non trouvée'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:150|unique:entite,nom,' . $id,
            'type' => 'nullable|string|max:50',
            'id_sup' => 'nullable|exists:entite,id',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier qu'on ne crée pas de boucle (une entité ne peut pas être son propre parent)
        if ($request->id_sup && $request->id_sup == $id) {
            return response()->json([
                'message' => 'Une entité ne peut pas être son propre parent'
            ], 422);
        }

        $old = $entite->getOriginal();
        $entite->update($request->all());
        $entite->load(['parent', 'enfants']);

        AuditLogger::log($request, 'updated', 'Entite', $entite->id, $entite->nom, $old, $request->all());

        return response()->json($entite);
    }

    /**
     * Supprime une entité
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $entite = Entite::find($id);

        if (!$entite) {
            return response()->json([
                'message' => 'Entité non trouvée'
            ], 404);
        }

        // Vérifier si l'entité a des enfants
        if ($entite->enfants()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette entité car elle a des entités dépendantes'
            ], 422);
        }

        $old = $entite->getOriginal();
        $label = $entite->nom;
        $entite->delete();

        AuditLogger::log($request, 'deleted', 'Entite', $id, $label, $old, null);

        return response()->json([
            'message' => 'Entité supprimée avec succès'
        ]);
    }
}
