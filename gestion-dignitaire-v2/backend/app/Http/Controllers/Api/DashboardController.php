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
                'totalActifs' => DB::table('dignitaire')->where('statut', 'actif')->count(),
                'totalRetraites' => DB::table('dignitaire')->where('statut', 'retraite')->count(),
                'totalNonLocalises' => DB::table('dignitaire')->where('statut', 'non_localise')->count(),
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

            // Répartition par statut
            $parStatut = DB::table('dignitaire')
                ->select('statut as nom', DB::raw('COUNT(*) as count'))
                ->groupBy('statut')
                ->get()
                ->map(function ($row) {
                    $row->nom = ['actif' => 'Actif', 'retraite' => 'Retraité', 'non_localise' => 'Non localisé'][$row->nom] ?? $row->nom;
                    return $row;
                });

            // Évolution des nominations créées sur les 12 derniers mois
            $nominationsParMois = DB::table('nominations')
                ->selectRaw("DATE_FORMAT(date_debut, '%Y-%m') as mois, COUNT(*) as count")
                ->where('date_debut', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

            // Évolution des candidatures traitées (validées) sur les 12 derniers mois
            $candidaturesParMois = \App\Models\Candidat::valide()
                ->whereNotNull('date_validation')
                ->where('date_validation', '>=', now()->subMonths(11)->startOfMonth())
                ->get()
                ->groupBy(fn ($c) => $c->date_validation->format('Y-m'))
                ->map(fn ($group, $mois) => (object) ['mois' => $mois, 'count' => $group->count()])
                ->values();

            return response()->json([
                'parGenre' => $parGenre,
                'parRegion' => $parRegion,
                'parPoste' => $parPoste,
                'parStatut' => $parStatut,
                'nominationsParMois' => $nominationsParMois,
                'candidaturesParMois' => $candidaturesParMois,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des données graphiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
