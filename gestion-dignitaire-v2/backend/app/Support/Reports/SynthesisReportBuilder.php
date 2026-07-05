<?php

namespace App\Support\Reports;

use App\Models\Candidat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Construit les données du rapport de synthèse périodique (mensuel,
 * trimestriel, annuel) : totaux globaux + répartitions + compteurs
 * sur la période concernée.
 */
class SynthesisReportBuilder
{
    public function buildData(Carbon $debut, Carbon $fin): array
    {
        return [
            'totaux' => [
                'totalDignitaires' => DB::table('dignitaire')->count(),
                'totalPostes' => DB::table('postes')->count(),
                'totalNominations' => DB::table('nominations')->count(),
                'totalDecorations' => DB::table('decoration')->count(),
                'totalDiplomes' => DB::table('diplome')->count(),
            ],
            'parGenre' => [
                'hommes' => DB::table('dignitaire')->where('genre', 'Homme')->count(),
                'femmes' => DB::table('dignitaire')->where('genre', 'Femme')->count(),
            ],
            'parStatut' => DB::table('dignitaire')
                ->select('statut as nom', DB::raw('COUNT(*) as count'))
                ->groupBy('statut')
                ->get()
                ->map(function ($row) {
                    $row->nom = ['actif' => 'Actif', 'retraite' => 'Retraité', 'non_localise' => 'Non localisé'][$row->nom] ?? $row->nom;
                    return $row;
                }),
            'parRegion' => DB::table('dignitaire')
                ->join('ville', 'dignitaire.lieu_naissance', '=', 'ville.id')
                ->join('region', 'ville.region_id', '=', 'region.id')
                ->select('region.nom as nom', DB::raw('COUNT(*) as count'))
                ->whereNotNull('ville.region_id')
                ->groupBy('region.id', 'region.nom')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'periode' => [
                'nominationsCreees' => DB::table('nominations')->whereBetween('date_debut', [$debut, $fin])->count(),
                'postesCrees' => DB::table('postes')->whereBetween('date_debut', [$debut, $fin])->count(),
                'decorationsAttribuees' => DB::table('decoration_dignitaire')->whereBetween('date_attribution', [$debut, $fin])->count(),
                'diplomesObtenus' => DB::table('diplome')->whereBetween(DB::raw('CAST(annee AS UNSIGNED)'), [$debut->year, $fin->year])->count(),
                'candidaturesValidees' => Candidat::where('statut', 'valide')->whereBetween('date_validation', [$debut, $fin])->count(),
                'candidaturesRefusees' => Candidat::where('statut', 'refuse')->whereBetween('date_validation', [$debut, $fin])->count(),
            ],
        ];
    }
}
