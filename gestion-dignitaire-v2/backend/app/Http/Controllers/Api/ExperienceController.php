<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExperienceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('experiences as e')
            ->select([
                'e.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                's.nom as structure_nom'
            ])
            ->leftJoin('dignitaire as d', 'e.dignitaire_id', '=', 'd.id')
            ->leftJoin('structure as s', 'e.structure_id', '=', 's.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('e.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('e.intitule', 'like', "%{$search}%")
                  ->orWhere('s.nom', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(d.prenom, ' ', d.nom)"), 'like', "%{$search}%");
            });
        }

        $experiences = $query->orderBy('e.date_debut', 'desc')->get();

        return response()->json($experiences);
    }

    public function show(int $id): JsonResponse
    {
        $experience = DB::table('experiences as e')
            ->select([
                'e.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                's.nom as structure_nom'
            ])
            ->leftJoin('dignitaire as d', 'e.dignitaire_id', '=', 'd.id')
            ->leftJoin('structure as s', 'e.structure_id', '=', 's.id')
            ->where('e.id', $id)
            ->first();

        if (!$experience) {
            return response()->json(['message' => 'Expérience non trouvée'], 404);
        }

        return response()->json($experience);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'structure_id' => 'nullable|exists:structure,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'justificatif' => 'nullable|file|max:10240|mimes:pdf',
        ]);

        if ($request->hasFile('justificatif')) {
            $validated['justificatif_path'] = $request->file('justificatif')
                ->store('dignitaires/experiences', 'public');
        }
        unset($validated['justificatif']);

        $id = DB::table('experiences')->insertGetId($validated);

        AuditLogger::log($request, 'created', 'Experience', $id, $validated['intitule'] ?? null, null, $validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $experience = DB::table('experiences')->where('id', $id)->first();
        if (!$experience) {
            return response()->json(['message' => 'Expérience non trouvée'], 404);
        }

        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'structure_id' => 'nullable|exists:structure,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'justificatif' => 'nullable|file|max:10240|mimes:pdf',
        ]);

        $oldPath = $experience->justificatif_path;
        $newPath = null;
        if ($request->hasFile('justificatif')) {
            $newPath = $request->file('justificatif')
                ->store('dignitaires/experiences', 'public');
            $validated['justificatif_path'] = $newPath;
        }
        unset($validated['justificatif']);

        $old = (array) $experience;
        try {
            DB::table('experiences')->where('id', $id)->update($validated);
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        AuditLogger::log($request, 'updated', 'Experience', $id, $validated['intitule'] ?? null, $old, $validated);

        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $experience = DB::table('experiences')->where('id', $id)->first();
        if (!$experience) {
            return response()->json(['message' => 'Expérience non trouvée'], 404);
        }

        $old = (array) $experience;
        DB::table('experiences')->where('id', $id)->delete();

        if ($experience->justificatif_path) {
            Storage::disk('public')->delete($experience->justificatif_path);
        }

        AuditLogger::log($request, 'deleted', 'Experience', $id, $old['intitule'] ?? null, $old, null);

        return response()->json(['message' => 'Expérience supprimée avec succès']);
    }

    public function downloadJustificatif(int $id)
    {
        $experience = DB::table('experiences')->where('id', $id)->first();
        if (!$experience) {
            return response()->json(['message' => 'Expérience non trouvée'], 404);
        }

        if (!$experience->justificatif_path || !Storage::disk('public')->exists($experience->justificatif_path)) {
            return response()->json(['message' => 'Aucun justificatif disponible pour cette expérience'], 404);
        }

        $filename = 'justificatif-experience-' . $experience->id . '.pdf';

        return Storage::disk('public')->download($experience->justificatif_path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
