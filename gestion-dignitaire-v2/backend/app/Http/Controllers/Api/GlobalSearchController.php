<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Models\Decoration;
use App\Models\Dignitaire;
use App\Models\Diplome;
use App\Models\Entite;
use App\Models\Nomination;
use App\Models\Poste;
use App\Support\Permissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Recherche globale transverse demandée en réunion ("recherche globale
 * intelligente") : une seule requête interroge dignitaires, candidats,
 * nominations, postes, diplômes, décorations et entités, et renvoie des
 * résultats groupés par type avec un libellé et une route frontend cible.
 */
class GlobalSearchController extends Controller
{
    private const LIMIT_PAR_TYPE = 5;

    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['query' => $q, 'results' => []]);
        }

        $results = [];

        $dignitaires = Dignitaire::query()
            ->where(function ($query) use ($q) {
                $query->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('matricule', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%");
            })
            ->limit(self::LIMIT_PAR_TYPE)
            ->get();

        foreach ($dignitaires as $d) {
            $results[] = [
                'type' => 'dignitaire',
                'type_label' => 'Dignitaire',
                'id' => $d->id,
                'label' => trim("{$d->prenom} {$d->nom}"),
                'sublabel' => $d->matricule,
                'url' => "/dignitaires/{$d->id}",
            ];
        }

        // Données de candidature réservées aux profils avec accès complet
        // (mêmes droits que la page /admin/candidatures).
        if (Permissions::aAccesComplet($request->user())) {
            $candidats = Candidat::query()
                ->where(function ($query) use ($q) {
                    $query->where('nom', 'like', "%{$q}%")
                        ->orWhere('prenom', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('matricule', 'like', "%{$q}%");
                })
                ->limit(self::LIMIT_PAR_TYPE)
                ->get();

            foreach ($candidats as $c) {
                $results[] = [
                    'type' => 'candidat',
                    'type_label' => 'Candidature',
                    'id' => $c->id,
                    'label' => trim("{$c->prenom} {$c->nom}"),
                    'sublabel' => $c->email,
                    'url' => "/admin/candidatures/{$c->id}",
                ];
            }
        }

        $nominations = Nomination::with('dignitaire')
            ->where(function ($query) use ($q) {
                $query->where('fonction', 'like', "%{$q}%")
                    ->orWhere('numero_decret', 'like', "%{$q}%");
            })
            ->limit(self::LIMIT_PAR_TYPE)
            ->get();

        foreach ($nominations as $n) {
            $results[] = [
                'type' => 'nomination',
                'type_label' => 'Nomination',
                'id' => $n->id,
                'label' => $n->fonction ?: ('Nomination #' . $n->id),
                'sublabel' => $n->dignitaire?->nom_complet,
                'url' => '/nominations',
            ];
        }

        $postes = Poste::with('dignitaire')
            ->where('intitule', 'like', "%{$q}%")
            ->limit(self::LIMIT_PAR_TYPE)
            ->get();

        foreach ($postes as $p) {
            $results[] = [
                'type' => 'poste',
                'type_label' => 'Poste',
                'id' => $p->id,
                'label' => $p->intitule,
                'sublabel' => $p->dignitaire?->nom_complet,
                'url' => '/postes',
            ];
        }

        $diplomes = Diplome::with('dignitaire')
            ->where('intitule', 'like', "%{$q}%")
            ->limit(self::LIMIT_PAR_TYPE)
            ->get();

        foreach ($diplomes as $dp) {
            $results[] = [
                'type' => 'diplome',
                'type_label' => 'Diplôme',
                'id' => $dp->id,
                'label' => $dp->intitule,
                'sublabel' => $dp->dignitaire?->nom_complet,
                'url' => '/diplomes',
            ];
        }

        $decorations = Decoration::where('deco_nom', 'like', "%{$q}%")
            ->limit(self::LIMIT_PAR_TYPE)
            ->get();

        foreach ($decorations as $dec) {
            $results[] = [
                'type' => 'decoration',
                'type_label' => 'Décoration',
                'id' => $dec->deco_id,
                'label' => $dec->deco_nom,
                'sublabel' => $dec->deco_type,
                'url' => '/decorations',
            ];
        }

        $entites = Entite::where('nom', 'like', "%{$q}%")
            ->limit(self::LIMIT_PAR_TYPE)
            ->get();

        foreach ($entites as $e) {
            $results[] = [
                'type' => 'entite',
                'type_label' => 'Entité',
                'id' => $e->id,
                'label' => $e->nom,
                'sublabel' => $e->type,
                'url' => '/entites',
            ];
        }

        return response()->json([
            'query' => $q,
            'results' => $results,
            'total' => count($results),
        ]);
    }
}
