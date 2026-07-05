<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rapport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Archive des rapports périodiques générés automatiquement
 * (mensuel/trimestriel/annuel) par la commande `rapports:generer`.
 */
class RapportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Rapport::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 20);
        $rapports = $query->orderByDesc('genere_le')->paginate($perPage);

        return response()->json($rapports);
    }

    public function download(int $id)
    {
        $rapport = Rapport::findOrFail($id);

        return Storage::download($rapport->chemin_fichier, $rapport->nom_fichier);
    }
}
