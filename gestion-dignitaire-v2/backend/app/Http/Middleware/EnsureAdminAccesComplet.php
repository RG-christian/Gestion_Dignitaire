<?php

namespace App\Http\Middleware;

use App\Support\Permissions;
use Closure;
use Illuminate\Http\Request;

/**
 * Réserve une route aux rôles Administrateur / Super Administrateur
 * (gestion des candidatures notamment : pas de sous-fonction dédiée pour
 * un contrôle plus fin, donc accès binaire admin/non-admin).
 */
class EnsureAdminAccesComplet
{
    public function handle(Request $request, Closure $next)
    {
        if (!Permissions::aAccesComplet($request->user())) {
            return response()->json(['message' => 'Réservé aux administrateurs.'], 403);
        }

        return $next($request);
    }
}
