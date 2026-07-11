<?php

namespace App\Support;

use App\Models\User;

/**
 * Point d'entrée unique pour les vérifications de permissions par rôle.
 *
 * Administrateur / Super Administrateur : accès complet partout.
 * Assistant / Gestionnaire : accès module par module, selon les
 * sous-fonctions qui leur sont assignées et le niveau (lecture/écriture)
 * choisi pour chacune. Aucun des deux ne peut jamais supprimer.
 *
 * $user peut aussi être un Candidat (guard Sanctum partagé) : ces
 * utilisateurs n'ont par définition aucun rôle admin, d'où le typage large
 * plutôt que `?User` — un Candidat ne doit jamais planter ces vérifications,
 * juste se voir refuser l'accès.
 */
class Permissions
{
    private const ROLES_ACCES_COMPLET = ['Administrateur', 'Super Administrateur'];

    public static function estSuperAdmin(mixed $user): bool
    {
        return $user instanceof User && $user->role_name === 'Super Administrateur';
    }

    public static function aAccesComplet(mixed $user): bool
    {
        return $user instanceof User && in_array($user->role_name, self::ROLES_ACCES_COMPLET, true);
    }

    public static function peutSupprimer(mixed $user): bool
    {
        return self::aAccesComplet($user);
    }

    public static function peutLire(mixed $user, string $sousfonction): bool
    {
        if (self::aAccesComplet($user)) {
            return true;
        }

        return self::niveauSousfonction($user, $sousfonction) !== null;
    }

    public static function peutEcrire(mixed $user, string $sousfonction): bool
    {
        if (self::aAccesComplet($user)) {
            return true;
        }

        return self::niveauSousfonction($user, $sousfonction) === 'ecriture';
    }

    private static function niveauSousfonction(mixed $user, string $sousfonction): ?string
    {
        if (!$user instanceof User) {
            return null;
        }

        $match = $user->sousfonctions->firstWhere('sousfonction_name', $sousfonction);

        return $match?->pivot?->niveau;
    }
}
