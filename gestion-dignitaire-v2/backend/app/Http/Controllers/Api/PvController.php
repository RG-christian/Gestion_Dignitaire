<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pv;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PvController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Pv::query()->withCount('nominations');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('numero', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut')->toString());
        }

        return response()->json($query->orderByDesc('date')->orderByDesc('id')->get());
    }

    public function show(int $id): JsonResponse
    {
        $pv = Pv::withCount('nominations')
            ->with(['nominations.dignitaire', 'nominations.entite', 'nominations.poste'])
            ->findOrFail($id);

        return response()->json($pv);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateData($request);
        $validated['statut'] = 'actif';

        if ($request->hasFile('fichier')) {
            $validated['fichier_path'] = $request->file('fichier')->store('dignitaires/pv', 'public');
        }
        unset($validated['fichier']);

        $pv = Pv::create($validated);
        AuditLogger::log($request, 'created', 'Pv', $pv->id, $pv->numero, null, $validated);

        return response()->json($pv->loadCount('nominations'), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $pv = Pv::findOrFail($id);
        $validated = $this->validateData($request, $pv->id);
        $old = $pv->getOriginal();

        if ($request->hasFile('fichier')) {
            $this->deleteStoredFile($pv->fichier_path);
            $validated['fichier_path'] = $request->file('fichier')->store('dignitaires/pv', 'public');
        }
        unset($validated['fichier']);

        $pv->update($validated);
        AuditLogger::log($request, 'updated', 'Pv', $pv->id, $pv->numero, $old, $validated);

        return response()->json($pv->fresh()->loadCount('nominations'));
    }

    public function archiver(Request $request, int $id): JsonResponse
    {
        $pv = Pv::findOrFail($id);
        $old = $pv->getOriginal();
        $pv->update(['statut' => 'archive', 'archive_le' => now()]);
        AuditLogger::log($request, 'archived', 'Pv', $pv->id, $pv->numero, $old, $pv->fresh()->getAttributes());

        return response()->json($pv->fresh()->loadCount('nominations'));
    }

    public function restaurer(Request $request, int $id): JsonResponse
    {
        $pv = Pv::findOrFail($id);
        $old = $pv->getOriginal();
        $pv->update(['statut' => 'actif', 'archive_le' => null]);
        AuditLogger::log($request, 'restored', 'Pv', $pv->id, $pv->numero, $old, $pv->fresh()->getAttributes());

        return response()->json($pv->fresh()->loadCount('nominations'));
    }

    public function download(int $id)
    {
        $pv = Pv::findOrFail($id);
        if (!$pv->fichier_path || !Storage::disk('public')->exists($pv->fichier_path)) {
            return response()->json(['message' => 'Fichier du procès-verbal introuvable.'], 404);
        }

        return Storage::disk('public')->download($pv->fichier_path, "PV-{$pv->numero}.pdf");
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $pv = Pv::withCount('nominations')->findOrFail($id);
        if ($pv->nominations_count > 0) {
            return response()->json([
                'message' => 'Ce procès-verbal est lié à une nomination. Archivez-le au lieu de le supprimer.',
            ], 409);
        }

        $old = $pv->getOriginal();
        $this->deleteStoredFile($pv->fichier_path);
        $pv->delete();
        AuditLogger::log($request, 'deleted', 'Pv', $id, $old['numero'] ?? null, $old, null);

        return response()->json(['message' => 'Procès-verbal supprimé avec succès.']);
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'numero' => ['required', 'string', 'max:50', Rule::unique('pv', 'numero')->ignore($id)],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'fichier' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
