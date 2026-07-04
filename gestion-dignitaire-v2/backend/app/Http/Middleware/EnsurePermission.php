<?php

namespace App\Http\Middleware;

use App\Support\Permissions;
use Closure;
use Illuminate\Http\Request;

/**
 * Vérifie que l'utilisateur connecté a le droit de lire/écrire/supprimer
 * la ressource associée à la sous-fonction donnée, déduit de la méthode
 * HTTP de la requête.
 *
 * Usage : Route::middleware('permission:Dignitaire')->group(...)
 */
class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $sousfonction)
    {
        $user = $request->user();

        if ($request->isMethod('delete')) {
            if (!Permissions::peutSupprimer($user)) {
                return response()->json(['message' => 'Action non autorisée pour votre rôle.'], 403);
            }
        } elseif (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            if (!Permissions::peutEcrire($user, $sousfonction)) {
                return response()->json(['message' => 'Vous avez un accès en lecture seule sur ce module.'], 403);
            }
        } else {
            if (!Permissions::peutLire($user, $sousfonction)) {
                return response()->json(['message' => 'Vous n\'avez pas accès à ce module.'], 403);
            }
        }

        return $next($request);
    }
}
