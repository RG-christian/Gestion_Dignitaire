<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Domaine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomaineController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateData($request);
        $this->ensureUniqueName($validated['nom']);

        $domaine = Domaine::create($validated);
        AuditLogger::log($request, 'created', 'Domaine', $domaine->id, $domaine->nom, null, $validated);

        return response()->json($domaine, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Domaine::findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $domaine = Domaine::findOrFail($id);
        $validated = $this->validateData($request);
        $this->ensureUniqueName($validated['nom'], $id);
        $old = $domaine->getOriginal();
        $domaine->update($validated);
        AuditLogger::log($request, 'updated', 'Domaine', $id, $domaine->nom, $old, $validated);

        return response()->json($domaine->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $domaine = Domaine::findOrFail($id);
        $usages = DB::table('diplome')->where('domaine_id', $id)->count();
        if ($usages > 0) {
            return response()->json([
                'message' => "Ce domaine est utilisé par {$usages} diplôme(s) et ne peut pas être supprimé.",
                'usages' => $usages,
            ], 409);
        }

        $old = $domaine->getOriginal();
        $label = $domaine->nom;
        $domaine->delete();
        AuditLogger::log($request, 'deleted', 'Domaine', $id, $label, $old, null);

        return response()->json(['message' => 'Domaine supprimé avec succès.']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
    }

    private function ensureUniqueName(string $name, ?int $exceptId = null): void
    {
        $query = Domaine::whereRaw('LOWER(nom) = ?', [mb_strtolower(trim($name))]);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        if ($query->exists()) {
            abort(422, 'Un domaine portant ce nom existe déjà.');
        }
    }
}
