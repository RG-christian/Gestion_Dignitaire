<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NominationCreee;
use App\Models\Dignitaire;
use App\Models\Nomination;
use App\Support\AuditLogger;
use App\Support\Exports\GenericArrayExport;
use App\Support\Exports\ListPdfExporter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class NominationController extends Controller
{
    private function baseQuery(Request $request)
    {
        $query = DB::table('nominations as n')
            ->select([
                'n.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'p.intitule as poste_nom'
            ])
            ->leftJoin('dignitaire as d', 'n.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'n.entite_id', '=', 'e.id')
            ->leftJoin('postes as p', 'n.poste_id', '=', 'p.id');

        if ($request->has('dignitaire_id') && $request->dignitaire_id) {
            $query->where('n.dignitaire_id', $request->dignitaire_id);
        }

        if ($request->has('entite_id') && $request->entite_id) {
            $query->where('n.entite_id', $request->entite_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('n.fonction', 'like', "%{$search}%")
                  ->orWhere('e.nom', 'like', "%{$search}%")
                  ->orWhere('p.intitule', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(d.prenom, ' ', d.nom)"), 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        $nominations = $this->baseQuery($request)->orderBy('n.date_debut', 'desc')->get();

        return response()->json($nominations);
    }

    private function filtresResume(Request $request): ?string
    {
        $parts = [];
        if ($request->filled('search')) $parts[] = "Recherche: {$request->search}";
        if ($request->filled('dignitaire_id')) $parts[] = "Dignitaire #{$request->dignitaire_id}";
        if ($request->filled('entite_id')) $parts[] = "Entité #{$request->entite_id}";

        return $parts ? implode(' — ', $parts) : null;
    }

    /**
     * Export de la liste des nominations (PDF ou Excel), avec les mêmes
     * filtres que index().
     */
    public function export(Request $request)
    {
        $rows = $this->baseQuery($request)->orderBy('n.date_debut', 'desc')->get();

        $headings = ['Dignitaire', 'Entité', 'Poste', 'Fonction', 'Date début', 'Date fin', 'Statut'];
        $data = $rows->map(fn ($n) => [
            $n->dignitaire_nom,
            $n->entite_nom,
            $n->poste_nom,
            $n->fonction,
            $n->date_debut,
            $n->date_fin,
            $n->statut,
        ]);

        if ($request->get('format') === 'excel') {
            return Excel::download(new GenericArrayExport($headings, $data, 'Nominations'), 'nominations.xlsx');
        }

        return app(ListPdfExporter::class)
            ->render('Liste des nominations', $headings, $data, $this->filtresResume($request))
            ->download('nominations.pdf');
    }

    public function show(int $id): JsonResponse
    {
        $nomination = DB::table('nominations as n')
            ->select([
                'n.*',
                DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
                'e.nom as entite_nom',
                'p.intitule as poste_nom'
            ])
            ->leftJoin('dignitaire as d', 'n.dignitaire_id', '=', 'd.id')
            ->leftJoin('entite as e', 'n.entite_id', '=', 'e.id')
            ->leftJoin('postes as p', 'n.poste_id', '=', 'p.id')
            ->where('n.id', $id)
            ->first();

        if (!$nomination) {
            return response()->json(['message' => 'Nomination non trouvée'], 404);
        }

        return response()->json($nomination);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'entite_id' => 'nullable|exists:entite,id',
            'poste_id' => 'nullable|exists:postes,id',
            'fonction' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'numero_decret' => 'nullable|string|max:100',
        ]);

        $validated['statut'] = 'en_cours';

        $id = DB::table('nominations')->insertGetId($validated);

        AuditLogger::log($request, 'created', 'Nomination', $id, $validated['fonction'] ?? null, null, $validated);

        $dignitaire = Dignitaire::find($validated['dignitaire_id']);
        $email = $dignitaire?->emailNotification();

        if ($dignitaire && $email) {
            try {
                Mail::to($email)->send(new NominationCreee($dignitaire, Nomination::find($id)));
            } catch (\Exception $e) {
                Log::warning('Echec envoi email de nomination créée', [
                    'nomination_id' => $id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['id' => $id, ...$validated], 201);
    }

    /**
     * Clôturer une nomination : fin de fonction formelle ou mise à
     * disposition de l'administration d'origine.
     *
     * POST /api/nominations/{id}/cloturer
     */
    public function cloturer(Request $request, int $id): JsonResponse
    {
        $old = (array) DB::table('nominations')->where('id', $id)->first();

        if (!$old) {
            return response()->json(['message' => 'Nomination non trouvée'], 404);
        }

        $validated = $request->validate([
            'motif_fin' => 'required|in:fin_fonction,mise_a_disposition',
            'date_fin' => 'nullable|date',
        ]);

        $update = [
            'statut' => 'terminee',
            'motif_fin' => $validated['motif_fin'],
            'date_fin' => $validated['date_fin'] ?? now()->toDateString(),
        ];

        DB::table('nominations')->where('id', $id)->update($update);

        AuditLogger::log($request, 'cloturee', 'Nomination', $id, $old['fonction'] ?? null, $old, $update);

        return response()->json(['id' => $id, ...$update]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaire,id',
            'entite_id' => 'nullable|exists:entite,id',
            'poste_id' => 'nullable|exists:postes,id',
            'fonction' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'numero_decret' => 'nullable|string|max:100',
        ]);

        $old = (array) DB::table('nominations')->where('id', $id)->first();
        DB::table('nominations')->where('id', $id)->update($validated);

        AuditLogger::log($request, 'updated', 'Nomination', $id, $validated['fonction'] ?? null, $old, $validated);

        return response()->json(['id' => $id, ...$validated]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $old = (array) DB::table('nominations')->where('id', $id)->first();
        DB::table('nominations')->where('id', $id)->delete();

        AuditLogger::log($request, 'deleted', 'Nomination', $id, $old['fonction'] ?? null, $old, null);

        return response()->json(['message' => 'Nomination supprimée avec succès']);
    }
}
