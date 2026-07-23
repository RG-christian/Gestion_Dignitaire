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

    /**
     * Attache fonctions/sous-fonctions à l'utilisateur pour le menu latéral.
     *
     * Administrateur / Super Administrateur ont un accès complet qui ne
     * dépend jamais de leurs lignes user_fonctions/user_sousfonctions —
     * celles-ci ne servent qu'à des comptes historiques et sont donc
     * souvent incomplètes (un module ajouté après coup, comme "Entité" le
     * 2026-07-23, n'y figure pas automatiquement). On leur attache donc
     * toujours la liste complète des fonctions et sous-fonctions
     * existantes, pour que le menu reflète leur accès réel plutôt que des
     * lignes de pivot possiblement obsolètes. Pour Assistant/Gestionnaire,
     * on garde leurs propres assignations : c'est précisément ce qui
     * détermine ce à quoi ils ont droit.
     */
    public static function chargerFonctionsEtSousfonctions(User $user): void
    {
        if (self::aAccesComplet($user)) {
            $user->setRelation('fonctions', \App\Models\Fonction::orderBy('fonction_name')->get());
            $user->setRelation(
                'sousfonctions',
                \App\Models\Sousfonction::orderBy('sousfonction_name')->get()->each(function ($sf) {
                    $sf->setAttribute('pivot', (object) ['niveau' => 'ecriture']);
                })
            );
            return;
        }

        $user->load(['fonctions', 'sousfonctions']);
    }
}
