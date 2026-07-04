<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use App\Models\CandidatDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Contrôleur de gestion des documents des candidats
 */
class CandidatDocumentController extends Controller
{
    /**
     * Liste des documents d'un candidat
     * 
     * GET /api/candidats/me/documents
     */
    public function index(Request $request): JsonResponse
    {
        $candidat = $request->user();
        $documents = $candidat->documents()->latest()->get();

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    /**
     * Upload d'un document
     * 
     * POST /api/candidats/me/documents
     */
    public function store(Request $request): JsonResponse
    {
        $candidat = $request->user();

        if (!$candidat->estModifiable()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez plus ajouter de documents à votre candidature.'
            ], 403);
        }

        $request->validate([
            'type_document' => 'required|in:diplome,cv,lettre,attestation,casier,medical,passeport,autre',
            'fichier' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx', // 10 Mo max
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $file = $request->file('fichier');
            
            // Stocker le fichier
            $path = $file->store('candidats/documents', 'public');

            // Créer l'enregistrement
            $document = $candidat->documents()->create([
                'type_document' => $request->type_document,
                'nom_fichier' => $file->getClientOriginalName(),
                'chemin_fichier' => $path,
                'taille_fichier' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document ajouté avec succès',
                'document' => $document
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload du document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un document
     * 
     * DELETE /api/candidats/me/documents/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $candidat = $request->user();

        if (!$candidat->estModifiable()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez plus supprimer de documents de votre candidature.'
            ], 403);
        }

        $document = CandidatDocument::where('candidat_id', $candidat->id)
                                    ->findOrFail($id);

        try {
            $document->delete(); // Le fichier physique sera supprimé automatiquement via le boot()

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharger un document (pour admin)
     * 
     * GET /api/admin/candidats/{candidatId}/documents/{documentId}/download
     */
    public function download(int $candidatId, int $documentId): mixed
    {
        $document = CandidatDocument::where('candidat_id', $candidatId)
                                    ->findOrFail($documentId);

        if (!Storage::disk('public')->exists($document->chemin_fichier)) {
            return response()->json([
                'success' => false,
                'message' => 'Fichier introuvable'
            ], 404);
        }

        return Storage::disk('public')->download($document->chemin_fichier, $document->nom_fichier);
    }
}
