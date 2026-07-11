<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dignitaire;
use App\Support\AuditLogger;
use App\Support\Exports\GenericArrayExport;
use App\Support\Exports\ListPdfExporter;
use App\Support\Imports\DignitaireImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DignitaireController extends Controller
{
    private function baseQuery(Request $request)
    {
        $query = Dignitaire::query()
            ->select([
                'dignitaire.*',
                'ville.nom as lieu_naissance_nom',
                DB::raw('(SELECT intitule FROM postes WHERE postes.dignitaire_id = dignitaire.id AND (postes.date_fin IS NULL OR postes.date_fin >= NOW()) ORDER BY postes.date_debut DESC LIMIT 1) as poste_actuel'),
                DB::raw('(SELECT ville.nom FROM postes LEFT JOIN ville ON postes.ville_id = ville.id WHERE postes.dignitaire_id = dignitaire.id AND (postes.date_fin IS NULL OR postes.date_fin >= NOW()) ORDER BY postes.date_debut DESC LIMIT 1) as ville_poste'),
                DB::raw('(SELECT entite.nom FROM postes LEFT JOIN entite ON postes.entite_id = entite.id WHERE postes.dignitaire_id = dignitaire.id AND (postes.date_fin IS NULL OR postes.date_fin >= NOW()) ORDER BY postes.date_debut DESC LIMIT 1) as nom_entite'),
                DB::raw('(SELECT CONCAT(YEAR(postes.date_debut), " - ", COALESCE(YEAR(postes.date_fin), "à ce jour")) FROM postes WHERE postes.dignitaire_id = dignitaire.id AND (postes.date_fin IS NULL OR postes.date_fin >= NOW()) ORDER BY postes.date_debut DESC LIMIT 1) as poste_annee')
            ])
            ->leftJoin('ville', 'dignitaire.lieu_naissance', '=', 'ville.id')
            ->with(['postes:id,dignitaire_id,date_debut']);

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dignitaire.nom', 'like', "%{$search}%")
                  ->orWhere('dignitaire.prenom', 'like', "%{$search}%")
                  ->orWhere('dignitaire.matricule', 'like', "%{$search}%")
                  ->orWhere('dignitaire.nip', 'like', "%{$search}%");
            });
        }

        // Filtre par genre
        if ($request->has('genre') && $request->genre) {
            $query->where('dignitaire.genre', $request->genre);
        }

        // Filtre par ville
        if ($request->has('ville_id') && $request->ville_id) {
            $query->where('dignitaire.lieu_naissance', $request->ville_id);
        }

        // Filtre par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('dignitaire.statut', $request->statut);
        }

        // Filtre par entité (via postes)
        if ($request->has('entite_id') && $request->entite_id) {
            $query->whereExists(function ($q) use ($request) {
                $q->select(DB::raw(1))
                  ->from('postes')
                  ->whereColumn('postes.dignitaire_id', 'dignitaire.id')
                  ->where('postes.entite_id', $request->entite_id);
            });
        }

        return $query;
    }

    /**
     * Liste des dignitaires avec filtres et recherche
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->baseQuery($request);

        // Tri alphabétique par nom par défaut ; "anciennete" trie sur la date
        // de prise de fonction (les plus anciens en premier par défaut)
        $sortBy = $request->get('sort_by', 'nom');
        $sortOrder = $request->get('sort_order', 'asc');
        if ($sortBy === 'anciennete') {
            // Même repli que Dignitaire::getDateReferenceAncienneteAttribute() :
            // date_prise_fonction si renseignée, sinon date de début du plus ancien poste.
            $query->orderByRaw(
                'COALESCE(dignitaire.date_prise_fonction, (SELECT MIN(p.date_debut) FROM postes p WHERE p.dignitaire_id = dignitaire.id)) ' .
                ($sortOrder === 'desc' ? 'DESC' : 'ASC') . ' , dignitaire.nom ASC'
            );
        } else {
            $query->orderBy('dignitaire.' . $sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $dignitaires = $query->paginate($perPage);

        return response()->json($dignitaires);
    }

    private function filtresResume(Request $request): ?string
    {
        $parts = [];
        if ($request->filled('search')) $parts[] = "Recherche: {$request->search}";
        if ($request->filled('genre')) $parts[] = "Genre: {$request->genre}";
        if ($request->filled('statut')) $parts[] = "Statut: {$request->statut}";
        if ($request->filled('ville_id')) $parts[] = "Ville #{$request->ville_id}";
        if ($request->filled('entite_id')) $parts[] = "Entité #{$request->entite_id}";

        return $parts ? implode(' — ', $parts) : null;
    }

    /**
     * Export de la liste des dignitaires (PDF ou Excel), avec les mêmes
     * filtres que index().
     */
    public function export(Request $request)
    {
        $rows = $this->baseQuery($request)->orderBy('dignitaire.nom')->get();

        $headings = ['Nom', 'Prénom', 'NIP', 'Matricule', 'Genre', 'Statut', 'Poste actuel', 'Entité', 'Ville'];
        $data = $rows->map(fn ($d) => [
            $d->nom,
            $d->prenom,
            $d->nip,
            $d->matricule,
            $d->genre,
            $d->statut,
            $d->poste_actuel,
            $d->nom_entite,
            $d->ville_poste,
        ]);

        if ($request->get('format') === 'excel') {
            return Excel::download(new GenericArrayExport($headings, $data, 'Dignitaires'), 'dignitaires.xlsx');
        }

        return app(ListPdfExporter::class)
            ->render('Liste des dignitaires', $headings, $data, $this->filtresResume($request))
            ->download('dignitaires.pdf');
    }

    /**
     * Export PDF de la fiche complète d'un dignitaire.
     */
    public function exportFichePdf(int $id)
    {
        $dignitaire = Dignitaire::with([
            'lieuNaissance.pays',
            'diplomes.etablissement',
            'diplomes.domaine',
            'enfants.lieuNaissance',
            'languesParlees.langue',
            'experiences.structure',
            'postes.entite',
            'postes.ville',
            'nominations.entite',
            'nominations.poste',
            'decorations',
            'telephones',
            'emails',
            'conjoints',
        ])->findOrFail($id);

        $photoBase64 = null;
        if ($dignitaire->photo) {
            $path = public_path('uploads/photos/' . $dignitaire->photo);
            if (file_exists($path)) {
                $photoBase64 = 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        $pdf = Pdf::loadView('exports.pdf.dignitaire-fiche', [
            'dignitaire' => $dignitaire,
            'photoBase64' => $photoBase64,
            'genereLe' => now()->format('d/m/Y à H:i'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('fiche-' . $dignitaire->matricule . '.pdf');
    }

    /**
     * Détails d'un dignitaire
     */
    public function show(int $id): JsonResponse
    {
        $dignitaire = Dignitaire::with([
            'lieuNaissance.pays',
            'diplomes.etablissement',
            'diplomes.domaine',
            'enfants.lieuNaissance',
            'languesParlees.langue',
            'experiences.structure',
            'postes.entite',
            'postes.ville',
            'nominations.entite',
            'nominations.poste',
            'decorations',
            'telephones',
            'emails'
        ])->findOrFail($id);

        return response()->json($dignitaire);
    }

    /**
     * Créer un dignitaire
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:dignitaire',
            'matricule' => 'required|string|max:20|unique:dignitaire',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'date_prise_fonction' => 'nullable|date',
            'lieu_naissance' => 'nullable|exists:ville,id',
            'nationalite' => 'nullable|string|max:100',
            'genre' => 'nullable|in:Homme,Femme',
            'etat_civil' => 'nullable|string|max:20',
            'photo' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'statut' => 'nullable|in:actif,retraite,non_localise',
        ]);

        $dignitaire = Dignitaire::create($validated);

        AuditLogger::log($request, 'created', 'Dignitaire', $dignitaire->id, "{$dignitaire->prenom} {$dignitaire->nom}", null, $validated);

        return response()->json($dignitaire, 201);
    }

    /**
     * Mettre à jour un dignitaire
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($id);

        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:dignitaire,nip,' . $id,
            'matricule' => 'required|string|max:20|unique:dignitaire,matricule,' . $id,
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'date_prise_fonction' => 'nullable|date',
            'lieu_naissance' => 'nullable|exists:ville,id',
            'nationalite' => 'nullable|string|max:100',
            'genre' => 'nullable|in:Homme,Femme',
            'etat_civil' => 'nullable|string|max:20',
            'photo' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'statut' => 'nullable|in:actif,retraite,non_localise',
        ]);

        $old = $dignitaire->getOriginal();
        $dignitaire->update($validated);

        AuditLogger::log($request, 'updated', 'Dignitaire', $dignitaire->id, "{$dignitaire->prenom} {$dignitaire->nom}", $old, $validated);

        return response()->json($dignitaire);
    }

    /**
     * Téléverser/remplacer la photo d'un dignitaire.
     *
     * POST /api/dignitaires/{id}/photo
     */
    public function uploadPhoto(Request $request, int $id): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($id);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $file = $request->file('photo');
        $filename = $dignitaire->matricule . '_' . time() . '.' . $file->getClientOriginalExtension();

        $ancienPhoto = $dignitaire->photo;

        $file->move(public_path('uploads/photos'), $filename);

        if ($ancienPhoto && file_exists(public_path('uploads/photos/' . $ancienPhoto))) {
            @unlink(public_path('uploads/photos/' . $ancienPhoto));
        }

        $dignitaire->update(['photo' => $filename]);

        AuditLogger::log($request, 'updated', 'Dignitaire', $dignitaire->id, "{$dignitaire->prenom} {$dignitaire->nom}", ['photo' => $ancienPhoto], ['photo' => $filename]);

        return response()->json(['photo' => $filename]);
    }

    /**
     * Supprimer un dignitaire
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($id);
        $old = $dignitaire->getOriginal();
        $label = "{$dignitaire->prenom} {$dignitaire->nom}";
        $dignitaire->delete();

        AuditLogger::log($request, 'deleted', 'Dignitaire', $id, $label, $old, null);

        return response()->json(['message' => 'Dignitaire supprimé avec succès']);
    }

    /**
     * Télécharge un modèle Excel vierge pour l'import de dignitaires.
     */
    public function importTemplate()
    {
        $headings = ['Nom', 'Prenom', 'NIP', 'Matricule', 'Date naissance', 'Genre', 'Etat civil', 'Nationalite', 'Telephone', 'Adresse', 'Statut'];
        $exemple = ['Ondo', 'Jean', '', '', '1975-03-12', 'Homme', 'Marié(e)', 'Gabonaise', '074000000', 'Libreville', 'actif'];

        return Excel::download(
            new GenericArrayExport($headings, new Collection([$exemple]), 'Dignitaires'),
            'modele-import-dignitaires.xlsx'
        );
    }

    /**
     * Import Excel de dignitaires (CR de réunion). Chaque ligne valide est
     * créée ; les lignes en erreur sont renvoyées pour correction sans
     * bloquer les lignes valides.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new DignitaireImport();
        Excel::import($import, $request->file('fichier'));

        foreach ($import->crees as $dignitaire) {
            AuditLogger::log($request, 'created', 'Dignitaire', $dignitaire->id, "{$dignitaire->prenom} {$dignitaire->nom}", null, ['source' => 'import_excel']);
        }

        return response()->json([
            'nb_crees' => count($import->crees),
            'nb_erreurs' => count($import->erreurs),
            'erreurs' => $import->erreurs,
        ]);
    }

    /**
     * Statistiques du dashboard
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_dignitaires' => Dignitaire::count(),
            'total_postes' => \App\Models\Poste::count(),
            'total_decorations' => \App\Models\Decoration::count(),
            'total_villes' => \App\Models\Ville::count(),
            'par_genre' => Dignitaire::selectRaw('genre, COUNT(*) as count')
                ->groupBy('genre')
                ->get(),
            'par_statut' => Dignitaire::selectRaw('statut, COUNT(*) as count')
                ->groupBy('statut')
                ->get(),
        ]);
    }
}
