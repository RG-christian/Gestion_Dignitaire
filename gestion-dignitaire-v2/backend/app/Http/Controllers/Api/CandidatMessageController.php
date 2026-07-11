<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CandidatRecommandation;
use App\Models\Candidat;
use App\Models\CandidatMessage;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Recommandations/messages échangés autour d'une candidature : un admin
 * consulte un dossier et peut y laisser une ou plusieurs recommandations,
 * qui remontent côté candidat comme notification + message sur son
 * dashboard (et par email, comme pour valider/refuser).
 */
class CandidatMessageController extends Controller
{
    /**
     * Historique des messages d'une candidature (côté admin).
     *
     * GET /api/admin/candidats/{id}/messages
     */
    public function index(int $candidatId): JsonResponse
    {
        $candidat = Candidat::findOrFail($candidatId);

        return response()->json($candidat->messages()->get());
    }

    /**
     * Laisser une recommandation sur une candidature (côté admin).
     *
     * POST /api/admin/candidats/{id}/messages
     */
    public function store(Request $request, int $candidatId): JsonResponse
    {
        $candidat = Candidat::findOrFail($candidatId);

        $validated = $request->validate([
            'contenu' => 'required|string|min:3',
        ]);

        $auteur = $request->user();

        $message = CandidatMessage::create([
            'candidat_id' => $candidat->id,
            'user_id' => $auteur?->id,
            'user_label' => $auteur?->username ?? $auteur?->email,
            'type' => 'recommandation',
            'contenu' => $validated['contenu'],
        ]);

        AuditLogger::log($request, 'created', 'CandidatMessage', $message->id, "{$candidat->prenom} {$candidat->nom}", null, $validated);

        try {
            Mail::to($candidat->email)->send(new CandidatRecommandation($candidat, $validated['contenu']));
        } catch (\Exception $e) {
            Log::warning('Echec envoi email de recommandation candidature', [
                'candidat_id' => $candidat->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json($message, 201);
    }

    /**
     * Messages du candidat connecté, les plus récents en premier.
     *
     * GET /api/candidats/me/messages
     */
    public function mesMessages(Request $request): JsonResponse
    {
        $messages = $request->user()->messages()->get();

        return response()->json([
            'messages' => $messages,
            'non_lus' => $messages->where('lu', false)->count(),
        ]);
    }

    /**
     * Marquer un message comme lu (côté candidat).
     *
     * POST /api/candidats/me/messages/{id}/lu
     */
    public function marquerLu(Request $request, int $id): JsonResponse
    {
        $message = $request->user()->messages()->findOrFail($id);
        $message->marquerCommeLu();

        return response()->json($message);
    }

    /**
     * Marquer tous les messages du candidat connecté comme lus.
     *
     * POST /api/candidats/me/messages/tout-lu
     */
    public function toutMarquerLu(Request $request): JsonResponse
    {
        $request->user()->messages()->nonLus()->update(['lu' => true, 'lu_le' => now()]);

        return response()->json(['message' => 'Tous les messages ont été marqués comme lus.']);
    }
}
