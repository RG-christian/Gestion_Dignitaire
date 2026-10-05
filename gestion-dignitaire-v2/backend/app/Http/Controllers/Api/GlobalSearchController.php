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
use Illuminate\Support\Facades\DB;

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
        $user = $request->user();

        if (Permissions::peutLire($user, 'Dignitaire')) {
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
        }

        // Données de candidature réservées aux profils avec accès complet
        // (mêmes droits que la page /admin/candidatures).
        if (Permissions::aAccesComplet($user)) {
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

        if (Permissions::peutLire($user, 'Nomination')) {
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
        }

        if (Permissions::peutLire($user, 'Poste')) {
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
        }

        if (Permissions::peutLire($user, 'Diplôme')) {
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
        }

        if (Permissions::peutLire($user, 'Décoration')) {
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
        }

        // Entités : donnée de référence, recherche toujours accessible
        // (même logique que /entites en lecture — cf. routes/api.php).
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

    public function advanced(Request $request): JsonResponse
    {
        abort_unless(Permissions::peutLire($request->user(), 'Dignitaire'), 403);

        $filters = $request->validate([
            'q' => 'nullable|string|max:100',
            'poste' => 'nullable|string|max:255',
            'pays_affectation_id' => 'nullable|integer|exists:pays,id',
            'langue_id' => 'nullable|integer|exists:langue,id',
            'domaine_id' => 'nullable|integer|exists:domaine,id',
            'niveau_academique' => 'nullable|string|max:100',
            'structure_id' => 'nullable|integer|exists:structure,id',
            'mandat_actif' => 'nullable|boolean',
            'est_militaire' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Dignitaire::query()
            ->with([
                'lieuNaissance.pays',
                'postes' => fn ($q) => $q->whereNull('deleted_at')->orderByDesc('date_debut'),
            ]);

        if (!empty($filters['q'])) {
            $query->search(trim($filters['q']));
        }
        if (!empty($filters['poste'])) {
            $query->whereHas('postes', fn ($q) => $q
                ->whereNull('deleted_at')
                ->where('intitule', $filters['poste']));
        }
        if (!empty($filters['pays_affectation_id'])) {
            $query->whereHas('affectations', fn ($q) => $q
                ->whereNull('deleted_at')
                ->where('pays_id', $filters['pays_affectation_id']));
        }
        if (!empty($filters['langue_id'])) {
            $query->whereHas('languesParlees', fn ($q) => $q->where('langue_id', $filters['langue_id']));
        }
        if (!empty($filters['domaine_id'])) {
            $query->whereHas('diplomes', fn ($q) => $q->where('domaine_id', $filters['domaine_id']));
        }
        if (!empty($filters['niveau_academique'])) {
            $query->whereHas('diplomes', fn ($q) => $q->where('type', $filters['niveau_academique']));
        }
        if (!empty($filters['structure_id'])) {
            $query->whereHas('experiences', fn ($q) => $q->where('structure_id', $filters['structure_id']));
        }
        if ($request->has('mandat_actif')) {
            $activeMandate = fn ($q) => $q->whereNull('deleted_at')
                ->where('date_debut', '<=', today())
                ->where(fn ($dates) => $dates->whereNull('date_fin')->orWhere('date_fin', '>=', today()));
            $request->boolean('mandat_actif')
                ? $query->whereHas('nominations', $activeMandate)
                : $query->whereDoesntHave('nominations', $activeMandate);
        }
        if ($request->has('est_militaire')) {
            $query->where('est_militaire', $request->boolean('est_militaire'));
        }

        $results = $query->orderBy('nom')->orderBy('prenom')
            ->paginate((int) ($filters['per_page'] ?? 20))
            ->withQueryString();

        return response()->json([
            'success' => true,
            'filters' => $filters,
            'results' => $results,
        ]);
    }

    public function advancedOptions(Request $request): JsonResponse
    {
        abort_unless(Permissions::peutLire($request->user(), 'Dignitaire'), 403);

        return response()->json([
            'postes' => DB::table('postes')->whereNull('deleted_at')->whereNotNull('intitule')
                ->distinct()->orderBy('intitule')->pluck('intitule'),
            'pays' => DB::table('pays')->select('id', 'nom')->orderBy('nom')->get(),
            'langues' => DB::table('langue')->select('id', 'nom')->orderBy('nom')->get(),
            'domaines' => DB::table('domaine')->select('id', 'nom')->orderBy('nom')->get(),
            'niveaux_academiques' => DB::table('diplome')->whereNotNull('type')->where('type', '<>', '')
                ->distinct()->orderBy('type')->pluck('type'),
            'structures' => DB::table('structure')->select('id', 'nom')->orderBy('nom')->get(),
        ]);
    }
}
