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
 */
class Permissions
{
    private const ROLES_ACCES_COMPLET = ['Administrateur', 'Super Administrateur'];

    public static function estSuperAdmin(?User $user): bool
    {
        return $user?->role_name === 'Super Administrateur';
    }

    public static function aAccesComplet(?User $user): bool
    {
        return in_array($user?->role_name, self::ROLES_ACCES_COMPLET, true);
    }

    public static function peutSupprimer(?User $user): bool
    {
        return self::aAccesComplet($user);
    }

    public static function peutLire(?User $user, string $sousfonction): bool
    {
        if (self::aAccesComplet($user)) {
            return true;
        }

        return self::niveauSousfonction($user, $sousfonction) !== null;
    }

    public static function peutEcrire(?User $user, string $sousfonction): bool
    {
        if (self::aAccesComplet($user)) {
            return true;
        }

        return self::niveauSousfonction($user, $sousfonction) === 'ecriture';
    }

    private static function niveauSousfonction(?User $user, string $sousfonction): ?string
    {
        if (!$user) {
            return null;
        }

        $match = $user->sousfonctions->firstWhere('sousfonction_name', $sousfonction);

        return $match?->pivot?->niveau;
    }
}
