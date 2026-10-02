<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidat;
use App\Models\Dignitaire;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard complet optimisé (un seul appel)
     */
    public function index(Request $request)
    {
        try {
            return response()->json([
                'stats' => $this->buildStats(),
                'chartData' => $this->buildChartData(),
                'derniersDignitaires' => Dignitaire::with(['lieuNaissance.pays', 'postes.entite'])
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get(),
                'activiteRecente' => Permissions::aAccesComplet($request->user())
                    ? AuditLog::orderByDesc('created_at')->limit(6)->get()
                    : [],
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
            return response()->json($this->buildStats());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques publiques pour la page d'accueil (aucune authentification requise)
     */
    public function publicStats()
    {
        try {
            $candidaturesTraitees = \App\Models\Candidat::valide()->count();

            $delaiMoyenJours = \App\Models\Candidat::valide()
                ->whereNotNull('date_validation')
                ->get()
                ->map(fn ($candidat) => $candidat->date_candidature->diffInDays($candidat->date_validation))
                ->avg();

            return response()->json([
                'totalDignitaires' => DB::table('dignitaire')->count(),
                'totalPaysCouverts' => DB::table('pays')->count(),
                'candidaturesTraitees' => $candidaturesTraitees,
                'delaiMoyenJours' => $delaiMoyenJours !== null ? round($delaiMoyenJours) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des statistiques publiques',
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
            return response()->json($this->buildChartData());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des données graphiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function buildStats(): array
    {
        return [
            'totalDignitaires' => DB::table('dignitaire')->count(),
            'totalPostes' => DB::table('postes')->count(),
            'totalDecorations' => DB::table('decoration')->count(),
            'totalVilles' => DB::table('ville')->count(),
            'totalPays' => DB::table('pays')->count(),
            'totalRegions' => DB::table('region')->count(),
            'totalDiplomes' => DB::table('diplome')->count(),
            'totalActifs' => DB::table('dignitaire')->where('statut', 'actif')->count(),
            'totalRetraites' => DB::table('dignitaire')->where('statut', 'retraite')->count(),
            'totalNonLocalises' => DB::table('dignitaire')->where('statut', 'non_localise')->count(),
        ];
    }

    private function buildChartData(): array
    {
        $parGenre = [
            'hommes' => DB::table('dignitaire')->where('genre', 'Homme')->count(),
            'femmes' => DB::table('dignitaire')->where('genre', 'Femme')->count(),
        ];

        $parRegion = DB::table('dignitaire')
            ->join('ville', 'dignitaire.lieu_naissance', '=', 'ville.id')
            ->join('region', 'ville.region_id', '=', 'region.id')
            ->select('region.nom as nom', DB::raw('COUNT(*) as count'))
            ->whereNotNull('ville.region_id')
            ->groupBy('region.id', 'region.nom')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $parPoste = DB::table('postes')
            ->select('postes.intitule as nom', DB::raw('COUNT(*) as count'))
            ->groupBy('postes.intitule')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $parStatut = DB::table('dignitaire')
            ->select('statut as nom', DB::raw('COUNT(*) as count'))
            ->groupBy('statut')
            ->get()
            ->map(function ($row) {
                $row->nom = ['actif' => 'Actif', 'retraite' => 'Retraité', 'non_localise' => 'Non localisé'][$row->nom] ?? $row->nom;
                return $row;
            });

        $nominationsParMois = DB::table('nominations')
            ->selectRaw("DATE_FORMAT(date_debut, '%Y-%m') as mois, COUNT(*) as count")
            ->where('date_debut', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        $candidaturesParMois = Candidat::valide()
            ->whereNotNull('date_validation')
            ->where('date_validation', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(date_validation, '%Y-%m') as mois, COUNT(*) as count")
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        return [
            'parGenre' => $parGenre,
            'parRegion' => $parRegion,
            'parPoste' => $parPoste,
            'parStatut' => $parStatut,
            'nominationsParMois' => $nominationsParMois,
            'candidaturesParMois' => $candidaturesParMois,
        ];
    }
}
