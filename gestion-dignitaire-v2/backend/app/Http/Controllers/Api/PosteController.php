<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use App\Support\Exports\GenericArrayExport;
use App\Support\Exports\ListPdfExporter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PosteController extends Controller
{
    /**
     * Crée ou met à jour l'affectation liée à ce poste (même ville/pays,
     * mêmes dates), pour qu'un dignitaire affecté quelque part via un poste
     * (au Gabon ou à l'étranger, peu importe) apparaisse automatiquement
     * dans /affectations sans double saisie. N'agit que si le poste a une
     * ville ; les affectations créées manuellement (poste_id = null) ne
     * sont jamais touchées ici.
     */
    private function synchroniserAffectation(int $posteId, array $poste): void
    {
        if (empty($poste['ville_id'])) {
            return;
        }

        $paysId = DB::table('ville')->where('id', $poste['ville_id'])->value('pays_id');
        if (!$paysId) {
            return;
        }

        $donnees = [
            'dignitaire_id' => $poste['dignitaire_id'],
            'pays_id' => $paysId,
            'ville_id' => $poste['ville_id'],
            'date_debut' => $poste['date_debut'] ?? now()->toDateString(),
            'type_affectation' => $poste['intitule'] ?? null,
            // Toujours "principale" : cette ligne reflète le poste lui-même,
            // distincte des missions temporaires ajoutées manuellement.
            'nature' => 'principale',
            'updated_at' => now(),
        ];

        $existante = DB::table('affectations')->where('poste_id', $posteId)->first();

        if ($existante) {
            DB::table('affectations')->where('id', $existante->id)->update($donnees);
        } else {
            DB::table('affectations')->insert($donnees + [
                'poste_id' => $posteId,
                'statut' => 'en_cours',
                'created_at' => now(),
            ]);
        }
    }

    private function cloturerAffectationLiee(int $posteId, ?string $dateFin): void
    {
        DB::table('affectations')->where('poste_id', $posteId)->update([
            'statut' => 'terminee',
            'date_fin' => $dateFin ?? now()->toDateString(),
            'updated_at' => now(),
        ]);
    }

    private function supprimerAffectationLiee(int $posteId): void
    {
        DB::table('affectations')->where('poste_id', $posteId)->delete();
    }

    private function baseQuery(Request $request)
    {
        $query = DB::table('postes as p')
            ->select([
                'p.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'v.nom as ville_nom'
            ])
            ->leftJoin('dignitaire as d', 'p.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'p.entite_id', '=', 'e.id')
            ->leftJoin('ville as v', 'p.ville_id', '=', 'v.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('p.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('p.intitule', 'like', "%{$search}%")
                  ->orWhere('e.nom', 'like', "%{$search}%")
                  ->orWhere('v.nom', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(d.prenom, ' ', d.nom)"), 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        $postes = $this->baseQuery($request)->orderBy('p.date_debut', 'desc')->get();

        return response()->json($postes);
    }

    private function filtresResume(Request $request): ?string
    {
        $parts = [];
        if ($request->filled('search')) $parts[] = "Recherche: {$request->search}";
        if ($request->filled('dignitaire_id')) $parts[] = "Dignitaire #{$request->dignitaire_id}";

        return $parts ? implode(' — ', $parts) : null;
    }

    /**
     * Export de la liste des postes (PDF ou Excel), avec les mêmes
     * filtres que index().
     */
    public function export(Request $request)
    {
        $rows = $this->baseQuery($request)->orderBy('p.date_debut', 'desc')->get();

        $headings = ['Dignitaire', 'Intitulé', 'Entité', 'Ville', 'Date début', 'Date fin', 'Statut'];
        $data = $rows->map(fn ($p) => [
            $p->dignitaire_nom,
            $p->intitule,
            $p->entite_nom,
            $p->ville_nom,
            $p->date_debut,
            $p->date_fin,
            $p->statut,
        ]);

        if ($request->get('format') === 'excel') {
            return Excel::download(new GenericArrayExport($headings, $data, 'Postes'), 'postes.xlsx');
        }

        return app(ListPdfExporter::class)
            ->render('Liste des postes', $headings, $data, $this->filtresResume($request))
            ->download('postes.pdf');
    }

    public function show(int $id): JsonResponse
    {
        $poste = DB::table('postes as p')
            ->select([
                'p.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'v.nom as ville_nom'
            ])
            ->leftJoin('dignitaire as d', 'p.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'p.entite_id', '=', 'e.id')
            ->leftJoin('ville as v', 'p.ville_id', '=', 'v.id')
            ->where('p.id', $id)
            ->first();

        if (!$poste) {
            return response()->json(['message' => 'Poste non trouvé'], 404);
        }

        return response()->json($poste);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'entite_id' => 'nullable|exists:entite,id',
            'ville_id' => 'nullable|exists:ville,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $validated['statut'] = 'en_cours';

        $id = DB::table('postes')->insertGetId($validated);

        $this->synchroniserAffectation($id, $validated);

        AuditLogger::log($request, 'created', 'Poste', $id, $validated['intitule'] ?? null, null, $validated);

        return response()->json(['id' => $id, ...$validated], 201);
    }

    /**
     * Clôturer un poste : fin de fonction formelle ou mise à disposition
     * de l'administration d'origine.
     *
     * POST /api/postes/{id}/cloturer
     */
    public function cloturer(Request $request, int $id): JsonResponse
    {
        $old = (array) DB::table('postes')->where('id', $id)->first();

        if (!$old) {
            return response()->json(['message' => 'Poste non trouvé'], 404);
        }

        $dateFinRule = 'nullable|date';
        if (!empty($old['date_debut'])) {
            $dateFinRule .= '|after:' . $old['date_debut'];
        }

        $validated = $request->validate([
            'motif_fin' => 'required|in:fin_fonction,mise_a_disposition',
            'date_fin' => $dateFinRule,
        ]);

        $update = [
            'statut' => 'terminee',
            'motif_fin' => $validated['motif_fin'],
            'date_fin' => $validated['date_fin'] ?? now()->toDateString(),
        ];

        DB::table('postes')->where('id', $id)->update($update);

        $this->cloturerAffectationLiee($id, $update['date_fin']);

        AuditLogger::log($request, 'cloturee', 'Poste', $id, $old['intitule'] ?? null, $old, $update);

        return response()->json(['id' => $id, ...$update]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'intitule' => 'required|string|max:255',
            'entite_id' => 'nullable|exists:entite,id',
            'ville_id' => 'nullable|exists:ville,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $old = (array) DB::table('postes')->where('id', $id)->first();
        DB::table('postes')->where('id', $id)->update($validated);

        $this->synchroniserAffectation($id, $validated);

        AuditLogger::log($request, 'updated', 'Poste', $id, $validated['intitule'] ?? null, $old, $validated);

        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $old = (array) DB::table('postes')->where('id', $id)->first();
        DB::table('postes')->where('id', $id)->delete();

        $this->supprimerAffectationLiee($id);

        AuditLogger::log($request, 'deleted', 'Poste', $id, $old['intitule'] ?? null, $old, null);

        return response()->json(['message' => 'Poste supprimé avec succès']);
    }
}
