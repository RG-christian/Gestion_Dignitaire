<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Langue;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * CRUD du référentiel des langues (nom, code ISO, famille linguistique,
 * nombre de locuteurs). La lecture seule était déjà exposée via
 * ReferentielController::langues(); ce contrôleur ajoute create/update/delete.
 */
class LangueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Langue::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code_iso', 'like', "%{$search}%")
                  ->orWhere('famille', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('nom')->get());
    }

    public function show(int $id): JsonResponse
    {
        $langue = Langue::find($id);

        if (!$langue) {
            return response()->json(['message' => 'Langue non trouvée'], 404);
        }

        return response()->json($langue);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100|unique:langue,nom',
            'code_iso' => 'nullable|string|max:10',
            'famille' => 'nullable|string|max:100',
            'nb_locuteurs' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $langue = Langue::create($validator->validated());

        AuditLogger::log($request, 'created', 'Langue', $langue->id, $langue->nom, null, $validator->validated());

        return response()->json($langue, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $langue = Langue::find($id);

        if (!$langue) {
            return response()->json(['message' => 'Langue non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100|unique:langue,nom,' . $id,
            'code_iso' => 'nullable|string|max:10',
            'famille' => 'nullable|string|max:100',
            'nb_locuteurs' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $old = $langue->getOriginal();
        $langue->update($validator->validated());

        AuditLogger::log($request, 'updated', 'Langue', $langue->id, $langue->nom, $old, $validator->validated());

        return response()->json($langue);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $langue = Langue::find($id);

        if (!$langue) {
            return response()->json(['message' => 'Langue non trouvée'], 404);
        }

        $old = $langue->getOriginal();
        $label = $langue->nom;
        $langue->delete();

        AuditLogger::log($request, 'deleted', 'Langue', $id, $label, $old, null);

        return response()->json(['message' => 'Langue supprimée avec succès']);
    }
}
