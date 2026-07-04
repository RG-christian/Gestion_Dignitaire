<?php

namespace App\Http\Middleware;

use App\Support\Permissions;
use Closure;
use Illuminate\Http\Request;

/**
 * Réserve une route au Super Administrateur (gestion des comptes utilisateurs).
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Permissions::estSuperAdmin($request->user())) {
            return response()->json(['message' => 'Réservé au Super Administrateur.'], 403);
        }

        return $next($request);
    }
}
