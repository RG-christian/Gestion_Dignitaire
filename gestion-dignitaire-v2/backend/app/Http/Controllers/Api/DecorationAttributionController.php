<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DecorationAttribution;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DecorationAttributionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json($this->query()->whereNull('dd.deleted_at')->orderByDesc('dd.date_attribution')->get());
    }

    public function initialData(): JsonResponse
    {
        return response()->json([
            'decorations' => DB::table('decoration')->select('deco_id as id', 'deco_nom as nom')->orderBy('deco_nom')->get(),
            'dignitaires' => DB::table('dignitaire')->select('id', 'nom', 'prenom')->orderBy('nom')->get(),
            'postes' => DB::table('postes')->select('id', 'dignitaire_id', 'intitule', 'date_debut', 'date_fin')->orderByDesc('date_debut')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $this->storeAttestation($request, $data);
        $attribution = DecorationAttribution::create($data);
        AuditLogger::log($request, 'created', 'DecorationAttribution', $attribution->id, null, null, $data);
        return response()->json($this->query()->where('dd.id', $attribution->id)->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $attribution = DecorationAttribution::findOrFail($id);
        $old = $attribution->getOriginal();
        $data = $this->validated($request);
        $newPath = $this->storeAttestation($request, $data);
        try {
            $attribution->update($data);
        } catch (\Throwable $exception) {
            if ($newPath) Storage::disk('local')->delete($newPath);
            throw $exception;
        }
        if ($newPath && $old['attestation_path']) Storage::disk('local')->delete($old['attestation_path']);
        AuditLogger::log($request, 'updated', 'DecorationAttribution', $id, null, $old, $data);
        return response()->json($this->query()->where('dd.id', $id)->first());
    }

    public function download(int $id)
    {
        $attribution = DecorationAttribution::findOrFail($id);
        if (!$attribution->attestation_path || !Storage::disk('local')->exists($attribution->attestation_path)) {
            return response()->json(['message' => 'Aucune attestation disponible pour cette attribution'], 404);
        }
        return Storage::disk('local')->download(
            $attribution->attestation_path,
            $attribution->attestation_nom_original ?: "attestation-decoration-{$id}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $attribution = DecorationAttribution::findOrFail($id);
        $old = $attribution->getOriginal();
        $attribution->delete();
        AuditLogger::log($request, 'archived', 'DecorationAttribution', $id, null, $old, ['deleted_at' => $attribution->deleted_at]);
        return response()->json(['message' => 'Attribution archivée avec succès']);
    }

    public function restaurer(Request $request, int $id): JsonResponse
    {
        $attribution = DecorationAttribution::onlyTrashed()->findOrFail($id);
        $old = $attribution->getOriginal();
        $attribution->restore();
        AuditLogger::log($request, 'restored', 'DecorationAttribution', $id, null, $old, ['deleted_at' => null]);
        return response()->json(['message' => 'Attribution restaurée avec succès']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'dignitaire_id' => ['required', 'exists:dignitaire,id'],
            'decoration_id' => ['required', 'exists:decoration,deco_id'],
            'date_attribution' => ['required', 'date'],
            'poste_id' => [
                'nullable',
                Rule::exists('postes', 'id')->where(fn ($query) => $query->where('dignitaire_id', $request->integer('dignitaire_id'))),
            ],
            'attestation' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
    }

    private function storeAttestation(Request $request, array &$data): ?string
    {
        unset($data['attestation']);
        if (!$request->hasFile('attestation')) return null;
        $file = $request->file('attestation');
        $path = $file->store('decorations/attestations', 'local');
        $data['attestation_path'] = $path;
        $data['attestation_nom_original'] = $file->getClientOriginalName();
        $data['attestation_mime'] = $file->getMimeType();
        $data['attestation_taille'] = $file->getSize();
        $data['attestation_sha256'] = hash_file('sha256', $file->getRealPath());
        $data['attestation_legacy'] = null;
        return $path;
    }

    private function query()
    {
        return DB::table('decoration_dignitaire as dd')
            ->join('decoration as d', 'd.deco_id', '=', 'dd.decoration_id')
            ->join('dignitaire as dig', 'dig.id', '=', 'dd.dignitaire_id')
            ->leftJoin('postes as p', 'p.id', '=', 'dd.poste_id')
            ->select('dd.*', 'd.deco_nom as decoration_nom', DB::raw("CONCAT(dig.prenom, ' ', dig.nom) as dignitaire_nom"), 'p.intitule as poste_intitule');
    }
}
