<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Decoration;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DecorationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Decoration::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deco_nom', 'like', "%{$search}%")
                  ->orWhere('deco_type', 'like', "%{$search}%")
                  ->orWhere('deco_niveau', 'like', "%{$search}%")
                  ->orWhere('deco_grade', 'like', "%{$search}%");
            });
        }

        $decorations = $query->orderBy('deco_nom')->get();

        return response()->json($decorations);
    }

    public function show(int $id): JsonResponse
    {
        $decoration = Decoration::findOrFail($id);
        return response()->json($decoration);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'type' => 'nullable|string|max:50',
            'niveau' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'date_obtention' => 'nullable|date',
            'autorite' => 'nullable|string|max:50',
            'motif' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'fichier_attestation' => 'nullable|string|max:100',
        ]);

        // Mapper les noms de colonnes avec préfixe
        $data = [
            'deco_nom' => $validated['nom'],
            'deco_type' => $validated['type'] ?? null,
            'deco_niveau' => $validated['niveau'] ?? null,
            'deco_grade' => $validated['grade'] ?? null,
            'deco_date_obtention' => $validated['date_obtention'] ?? null,
            'deco_autorite' => $validated['autorite'] ?? null,
            'deco_motif' => $validated['motif'] ?? null,
            'deco_description' => $validated['description'] ?? null,
            'deco_fichierAttestation' => $validated['fichier_attestation'] ?? null,
        ];

        $decoration = Decoration::create($data);

        AuditLogger::log($request, 'created', 'Decoration', $decoration->id, $data['deco_nom'], null, $data);

        return response()->json($decoration, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $decoration = Decoration::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'type' => 'nullable|string|max:50',
            'niveau' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'date_obtention' => 'nullable|date',
            'autorite' => 'nullable|string|max:50',
            'motif' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'fichier_attestation' => 'nullable|string|max:100',
        ]);

        // Mapper les noms de colonnes avec préfixe
        $data = [
            'deco_nom' => $validated['nom'],
            'deco_type' => $validated['type'] ?? null,
            'deco_niveau' => $validated['niveau'] ?? null,
            'deco_grade' => $validated['grade'] ?? null,
            'deco_date_obtention' => $validated['date_obtention'] ?? null,
            'deco_autorite' => $validated['autorite'] ?? null,
            'deco_motif' => $validated['motif'] ?? null,
            'deco_description' => $validated['description'] ?? null,
            'deco_fichierAttestation' => $validated['fichier_attestation'] ?? null,
        ];

        $old = $decoration->getOriginal();
        $decoration->update($data);

        AuditLogger::log($request, 'updated', 'Decoration', $decoration->id, $data['deco_nom'], $old, $data);

        return response()->json($decoration);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $decoration = Decoration::findOrFail($id);
        $old = $decoration->getOriginal();
        $label = $decoration->deco_nom;
        $decoration->delete();

        AuditLogger::log($request, 'deleted', 'Decoration', $id, $label, $old, null);

        return response()->json(['message' => 'Décoration supprimée avec succès']);
    }
}
