<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Permet à une session qui vient de se faire évincer (401) de savoir si
     * c'est parce qu'une connexion "forcée" a eu lieu ailleurs récemment,
     * pour afficher un message explicite plutôt qu'une déconnexion muette.
     *
     * Volontairement public : à ce stade le token de la session évincée
     * n'est déjà plus valide, elle ne peut donc pas interroger une route
     * protégée. Ne révèle qu'un booléen sur un id numérique, sur une
     * fenêtre de 90s — rien de sensible.
     *
     * GET /api/session/verifier-eviction?type=admin|candidat&id=123
     */
    public function verifierEviction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:admin,candidat',
            'id' => 'required|integer',
        ]);

        $causerType = $validated['type'] === 'admin' ? User::class : Candidat::class;

        $evince = AuditLog::where('causer_type', $causerType)
            ->where('causer_id', $validated['id'])
            ->where('action', 'connexion_forcee')
            ->where('created_at', '>=', now()->subSeconds(90))
            ->exists();

        return response()->json(['evicted' => $evince]);
    }
}
