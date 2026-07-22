<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Gère les tokens de réinitialisation de mot de passe pour les deux guards
 * de l'app (admin et candidat), sur la table partagée password_reset_tokens
 * (désambiguïsée par `type`). Cette table n'est pas un modèle Eloquent :
 * clé primaire composite (email, type), pas de timestamps automatiques.
 */
class PasswordResetService
{
    private const EXPIRATION_MINUTES = 60;

    /**
     * Génère un nouveau token (remplace tout token existant pour cet
     * email+type) et retourne sa version en clair, à insérer dans le lien
     * envoyé par email — seule sa version hachée est stockée.
     */
    public static function creerToken(string $email, string $type): string
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email, 'type' => $type],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        return $token;
    }

    /**
     * Vérifie qu'un token est valide (correspond au hash stocké et n'a pas
     * expiré) pour cet email+type.
     */
    public static function verifierToken(string $email, string $token, string $type): bool
    {
        $row = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('type', $type)
            ->first();

        if (!$row) {
            return false;
        }

        if (now()->diffInMinutes($row->created_at) > self::EXPIRATION_MINUTES) {
            return false;
        }

        return Hash::check($token, $row->token);
    }

    /**
     * Supprime le token une fois utilisé (ou pour invalider une demande).
     */
    public static function oublierToken(string $email, string $type): void
    {
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('type', $type)
            ->delete();
    }
}
