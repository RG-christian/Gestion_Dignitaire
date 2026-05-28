<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard complet optimisé (un seul appel)
     */
    public function index()
    {
        try {
            return response()->json([
                'stats' => [
                    'totalDignitaires' => DB::table('dignitaire')->count(),
                    'totalPostes' => DB::table('postes')->count(),
                    'totalDecorations' => DB::table('decoration')->count(),
                    'totalVilles' => DB::table('ville')->count(),
                    'totalPays' => DB::table('pays')->count(),
                    'totalRegions' => DB::table('region')->count(),
                ],
                'derniersDignitaires' => \App\Models\Dignitaire::with(['lieuNaissance.pays', 'postes.entite'])
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get(),
                'user' => auth()->user()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération du dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques du dashboard
     */
    public function stats()
    {
        try {
            $stats = [
                'totalDignitaires' => DB::table('dignitaire')->count(),
                'totalPostes' => DB::table('postes')->count(),
                'totalDecorations' => DB::table('decoration')->count(),
                'totalVilles' => DB::table('ville')->count(),
                'totalPays' => DB::table('pays')->count(),
                'totalRegions' => DB::table('region')->count(),
                'totalDiplomes' => DB::table('diplome')->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les données pour les graphiques
     */
    public function chartData()
    {
        try {
            // Répartition par genre
            $parGenre = [
                'hommes' => DB::table('dignitaire')->where('genre', 'Homme')->count(),
                'femmes' => DB::table('dignitaire')->where('genre', 'Femme')->count(),
            ];

            // Répartition par région (utiliser les vraies données avec jointure)
            $parRegion = DB::table('dignitaire')
                ->join('ville', 'dignitaire.lieu_naissance', '=', 'ville.id')
                ->join('region', 'ville.region_id', '=', 'region.id')
                ->select('region.nom as nom', DB::raw('COUNT(*) as count'))
                ->whereNotNull('ville.region_id')
                ->groupBy('region.id', 'region.nom')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            // Répartition par poste (grouper par intitulé)
            $parPoste = DB::table('postes')
                ->select('postes.intitule as nom', DB::raw('COUNT(*) as count'))
                ->groupBy('postes.intitule')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'parGenre' => $parGenre,
                'parRegion' => $parRegion,
                'parPoste' => $parPoste,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des données graphiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
