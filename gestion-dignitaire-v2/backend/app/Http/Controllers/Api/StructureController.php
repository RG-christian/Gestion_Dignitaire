<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Exports\GenericArrayExport;
use App\Support\Exports\ListPdfExporter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StructureController extends Controller
{
    private function baseQuery(Request $request)
    {
        $query = DB::table('structure');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%");
        }

        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        $structures = $this->baseQuery($request)->orderBy('nom')->get();

        return response()->json($structures);
    }

    private function filtresResume(Request $request): ?string
    {
        return $request->filled('search') ? "Recherche: {$request->search}" : null;
    }

    /**
     * Export de la liste des structures (PDF ou Excel), avec les mêmes
     * filtres que index().
     */
    public function export(Request $request)
    {
        $rows = $this->baseQuery($request)->orderBy('nom')->get();

        $headings = ['Nom'];
        $data = $rows->map(fn ($s) => [$s->nom]);

        if ($request->get('format') === 'excel') {
            return Excel::download(new GenericArrayExport($headings, $data, 'Structures'), 'structures.xlsx');
        }

        return app(ListPdfExporter::class)
            ->render('Liste des structures', $headings, $data, $this->filtresResume($request))
            ->download('structures.pdf');
    }

    public function show(int $id): JsonResponse
    {
        $structure = DB::table('structure')->where('id', $id)->first();

        if (!$structure) {
            return response()->json(['message' => 'Structure non trouvée'], 404);
        }

        return response()->json($structure);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150|unique:structure,nom',
        ]);

        $id = DB::table('structure')->insertGetId($validated);
        
        return response()->json(['id' => $id, ...$validated], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150|unique:structure,nom,' . $id,
        ]);

        DB::table('structure')->where('id', $id)->update($validated);
        
        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('structure')->where('id', $id)->delete();
        return response()->json(['message' => 'Structure supprimée avec succès']);
    }
}
