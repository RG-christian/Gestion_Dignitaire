<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DecorationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('decoration as d')
            ->select(['d.*']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('d.nom', 'like', "%{$search}%")
                  ->orWhere('d.type', 'like', "%{$search}%")
                  ->orWhere('d.niveau', 'like', "%{$search}%")
                  ->orWhere('d.grade', 'like', "%{$search}%");
            });
        }

        $decorations = $query->orderBy('d.nom')->get();

        return response()->json($decorations);
    }

    public function show(int $id): JsonResponse
    {
        $decoration = DB::table('decoration')->where('id', $id)->first();

        if (!$decoration) {
            return response()->json(['message' => 'Décoration non trouvée'], 404);
        }

        return response()->json($decoration);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'niveau' => 'nullable|string|max:100',
            'grade' => 'nullable|string|max:100',
            'date_obtention' => 'nullable|date',
            'autorite' => 'nullable|string|max:255',
            'motif' => 'nullable|string',
            'description' => 'nullable|string',
            'fichier_attestation' => 'nullable|string|max:255',
        ]);

        $id = DB::table('decoration')->insertGetId($validated);
        
        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'niveau' => 'nullable|string|max:100',
            'grade' => 'nullable|string|max:100',
            'date_obtention' => 'nullable|date',
            'autorite' => 'nullable|string|max:255',
            'motif' => 'nullable|string',
            'description' => 'nullable|string',
            'fichier_attestation' => 'nullable|string|max:255',
        ]);

        DB::table('decoration')->where('id', $id)->update($validated);
        
        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('decoration')->where('id', $id)->delete();
        return response()->json(['message' => 'Décoration supprimée avec succès']);
    }
}
