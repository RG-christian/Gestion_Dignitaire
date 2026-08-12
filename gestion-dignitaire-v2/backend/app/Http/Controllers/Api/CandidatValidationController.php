<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CandidatValidationController extends Controller
{
    /**
     * Valider un document candidat
     */
    public function validerDocument(Request $request, $candidatId, $documentId)
    {
        try {
            $document = DB::table('candidat_documents')
                ->where('id', $documentId)
                ->where('candidat_id', $candidatId)
                ->first();

            if (!$document) {
                return response()->json(['message' => 'Document non trouvé'], 404);
            }

            DB::table('candidat_documents')
                ->where('id', $documentId)
                ->update([
                    'statut_validation' => 'valide',
                    'motif_rejet' => null,
                    'valide_le' => now(),
                    'valide_par' => Auth::id(),
                    'updated_at' => now()
                ]);

            // Créer une notification
            $this->creerNotification(
                $candidatId,
                'nouveau_document',
                'Document validé',
                "Votre document \"{$document->type_document}\" a été validé par l'administration."
            );

            return response()->json([
                'message' => 'Document validé avec succès',
                'document' => DB::table('candidat_documents')->where('id', $documentId)->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la validation: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rejeter un document candidat
     */
    public function rejeterDocument(Request $request, $candidatId, $documentId)
    {
        $request->validate([
            'motif' => 'required|string|min:10|max:500'
        ]);

        try {
            $document = DB::table('candidat_documents')
                ->where('id', $documentId)
                ->where('candidat_id', $candidatId)
                ->first();

            if (!$document) {
                return response()->json(['message' => 'Document non trouvé'], 404);
            }

            DB::table('candidat_documents')
                ->where('id', $documentId)
                ->update([
                    'statut_validation' => 'rejete',
                    'motif_rejet' => $request->motif,
                    'valide_le' => now(),
                    'valide_par' => Auth::id(),
                    'updated_at' => now()
                ]);

            // Créer une notification
            $this->creerNotification(
                $candidatId,
                'nouveau_document',
                'Document rejeté',
                "Votre document \"{$document->type_document}\" a été rejeté. Motif : {$request->motif}"
            );

            return response()->json([
                'message' => 'Document rejeté avec succès',
                'document' => DB::table('candidat_documents')->where('id', $documentId)->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du rejet: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Valider un diplôme candidat
     */
    public function validerDiplome(Request $request, $candidatId, $diplomeId)
    {
        try {
            $diplome = DB::table('candidat_diplomes')
                ->where('id', $diplomeId)
                ->where('candidat_id', $candidatId)
                ->first();

            if (!$diplome) {
                return response()->json(['message' => 'Diplôme non trouvé'], 404);
            }

            DB::table('candidat_diplomes')
                ->where('id', $diplomeId)
                ->update([
                    'statut_validation' => 'valide',
                    'motif_rejet' => null,
                    'valide_le' => now(),
                    'valide_par' => Auth::id(),
                    'updated_at' => now()
                ]);

            // Créer une notification
            $this->creerNotification(
                $candidatId,
                'nouveau_diplome',
                'Diplôme validé',
                "Votre diplôme \"{$diplome->intitule}\" a été validé par l'administration."
            );

            return response()->json([
                'message' => 'Diplôme validé avec succès',
                'diplome' => DB::table('candidat_diplomes')->where('id', $diplomeId)->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la validation: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rejeter un diplôme candidat
     */
    public function rejeterDiplome(Request $request, $candidatId, $diplomeId)
    {
        $request->validate([
            'motif' => 'required|string|min:10|max:500'
        ]);

        try {
            $diplome = DB::table('candidat_diplomes')
                ->where('id', $diplomeId)
                ->where('candidat_id', $candidatId)
                ->first();

            if (!$diplome) {
                return response()->json(['message' => 'Diplôme non trouvé'], 404);
            }

            DB::table('candidat_diplomes')
                ->where('id', $diplomeId)
                ->update([
                    'statut_validation' => 'rejete',
                    'motif_rejet' => $request->motif,
                    'valide_le' => now(),
                    'valide_par' => Auth::id(),
                    'updated_at' => now()
                ]);

            // Créer une notification
            $this->creerNotification(
                $candidatId,
                'nouveau_diplome',
                'Diplôme rejeté',
                "Votre diplôme \"{$diplome->intitule}\" a été rejeté. Motif : {$request->motif}"
            );

            return response()->json([
                'message' => 'Diplôme rejeté avec succès',
                'diplome' => DB::table('candidat_diplomes')->where('id', $diplomeId)->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du rejet: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Valider une expérience candidat
     */
    public function validerExperience(Request $request, $candidatId, $experienceId)
    {
        try {
            $experience = DB::table('candidat_experiences')
                ->where('id', $experienceId)
                ->where('candidat_id', $candidatId)
                ->first();

            if (!$experience) {
                return response()->json(['message' => 'Expérience non trouvée'], 404);
            }

            DB::table('candidat_experiences')
                ->where('id', $experienceId)
                ->update([
                    'statut_validation' => 'valide',
                    'motif_rejet' => null,
                    'valide_le' => now(),
                    'valide_par' => Auth::id(),
                    'updated_at' => now()
                ]);

            // Créer une notification
            $this->creerNotification(
                $candidatId,
                'nouvelle_experience',
                'Expérience validée',
                "Votre expérience \"{$experience->intitule}\" a été validée par l'administration."
            );

            return response()->json([
                'message' => 'Expérience validée avec succès',
                'experience' => DB::table('candidat_experiences')->where('id', $experienceId)->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la validation: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rejeter une expérience candidat
     */
    public function rejeterExperience(Request $request, $candidatId, $experienceId)
    {
        $request->validate([
            'motif' => 'required|string|min:10|max:500'
        ]);

        try {
            $experience = DB::table('candidat_experiences')
                ->where('id', $experienceId)
                ->where('candidat_id', $candidatId)
                ->first();

            if (!$experience) {
                return response()->json(['message' => 'Expérience non trouvée'], 404);
            }

            DB::table('candidat_experiences')
                ->where('id', $experienceId)
                ->update([
                    'statut_validation' => 'rejete',
                    'motif_rejet' => $request->motif,
                    'valide_le' => now(),
                    'valide_par' => Auth::id(),
                    'updated_at' => now()
                ]);

            // Créer une notification
            $this->creerNotification(
                $candidatId,
                'nouvelle_experience',
                'Expérience rejetée',
                "Votre expérience \"{$experience->intitule}\" a été rejetée. Motif : {$request->motif}"
            );

            return response()->json([
                'message' => 'Expérience rejetée avec succès',
                'experience' => DB::table('candidat_experiences')->where('id', $experienceId)->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du rejet: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Créer une notification admin
     */
    private function creerNotification($candidatId, $type, $titre, $message)
    {
        DB::table('admin_notifications')->insert([
            'candidat_id' => $candidatId,
            'type' => $type,
            'titre' => $titre,
            'message' => $message,
            'lu' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Récupérer les notifications admin
     */
    public function getNotifications(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $nonLues = $request->get('non_lues', false);

        $query = DB::table('admin_notifications')
            ->join('candidats', 'admin_notifications.candidat_id', '=', 'candidats.id')
            ->select(
                'admin_notifications.*',
                DB::raw("CONCAT(candidats.prenom, ' ', candidats.nom) as candidat_nom")
            )
            ->orderBy('admin_notifications.created_at', 'desc');

        if ($nonLues === 'true' || $nonLues === true) {
            $query->where('admin_notifications.lu', false);
        }

        $notifications = $query->paginate($perPage);

        return response()->json($notifications);
    }

    /**
     * Compter les notifications non lues
     */
    public function countNonLues()
    {
        $count = DB::table('admin_notifications')
            ->where('lu', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerLue($id)
    {
        DB::table('admin_notifications')
            ->where('id', $id)
            ->update([
                'lu' => true,
                'lu_le' => now(),
                'updated_at' => now()
            ]);

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function marquerToutesLues()
    {
        DB::table('admin_notifications')
            ->where('lu', false)
            ->update([
                'lu' => true,
                'lu_le' => now(),
                'updated_at' => now()
            ]);

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues']);
    }
}
