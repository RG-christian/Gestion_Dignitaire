<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dignitaire;
use App\Models\DignitaireDocument;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DignitaireDocumentController extends Controller
{
    /**
     * GET /api/dignitaires/{dignitaireId}/documents
     */
    public function index(int $dignitaireId): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($dignitaireId);

        $documents = $dignitaire->documents()
            ->orderBy('type_document')
            ->orderByDesc('date_emission')
            ->get();

        return response()->json(['success' => true, 'documents' => $documents]);
    }

    /**
     * POST /api/dignitaires/{dignitaireId}/documents
     */
    public function store(Request $request, int $dignitaireId): JsonResponse
    {
        $dignitaire = Dignitaire::findOrFail($dignitaireId);

        $validated = $request->validate([
            'type_document' => 'required|in:diplome,passeport,casier,medical,attestation,autre',
            'fichier' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
            'nom_document' => 'nullable|string|max:255',
            'numero_document' => 'nullable|string|max:100',
            'date_emission' => 'nullable|date',
            'date_expiration' => 'nullable|date',
            'organisme_emetteur' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $file = $request->file('fichier');
            $path = $file->store('dignitaires/documents', 'public');

            $document = $dignitaire->documents()->create([
                'type_document' => $validated['type_document'],
                'nom_document' => $validated['nom_document'] ?? null,
                'numero_document' => $validated['numero_document'] ?? null,
                'date_emission' => $validated['date_emission'] ?? null,
                'date_expiration' => $validated['date_expiration'] ?? null,
                'organisme_emetteur' => $validated['organisme_emetteur'] ?? null,
                'description' => $validated['description'] ?? null,
                'nom_fichier' => $file->getClientOriginalName(),
                'chemin_fichier' => $path,
                'taille_fichier' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
            ]);

            AuditLogger::log(
                $request,
                'created',
                'DignitaireDocument',
                $document->id,
                "{$document->type_document} - {$dignitaire->prenom} {$dignitaire->nom}",
                null,
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Document ajouté avec succès',
                'document' => $document,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload du document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/dignitaire-documents/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $document = DignitaireDocument::findOrFail($id);

        try {
            $old = $document->getOriginal();
            $label = "{$document->type_document} - {$document->nom_fichier}";
            $document->delete();

            AuditLogger::log($request, 'deleted', 'DignitaireDocument', $id, $label, $old, null);

            return response()->json(['success' => true, 'message' => 'Document supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/dignitaire-documents/{id}/download
     */
    public function download(int $id)
    {
        $document = DignitaireDocument::findOrFail($id);

        if (!Storage::disk('public')->exists($document->chemin_fichier)) {
            return response()->json(['success' => false, 'message' => 'Fichier introuvable'], 404);
        }

        return Storage::disk('public')->download($document->chemin_fichier, $document->nom_fichier);
    }
}
