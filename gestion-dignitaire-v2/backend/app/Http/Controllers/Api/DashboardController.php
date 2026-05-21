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
}
