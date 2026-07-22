<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Génère et vérifie les codes OTP à 6 chiffres (table `otp_codes`), pour la
 * vérification d'email obligatoire à l'inscription candidat et pour la
 * double authentification optionnelle à la connexion (cf. [[Parametres]]).
 */
class OtpService
{
    private const EXPIRATION_MINUTES = 5;
    private const TENTATIVES_MAX = 5;

    /**
     * Génère un nouveau code (remplace tout code existant pour cet
     * email+type+purpose) et retourne sa version en clair, à envoyer par
     * email — seule sa version hachée est stockée.
     */
    public static function genererCode(string $email, string $type, string $purpose): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('otp_codes')->where('email', $email)->where('type', $type)->where('purpose', $purpose)->delete();

        DB::table('otp_codes')->insert([
            'email' => $email,
            'type' => $type,
            'purpose' => $purpose,
            'code' => Hash::make($code),
            'tentatives' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visible dans storage/logs/laravel.log uniquement hors production
        // (le code est sensible) — pratique pour tester sans dépendre de la
        // réception de l'email.
        if (!app()->isProduction()) {
            Log::info("OTP [{$type}/{$purpose}] pour {$email} : {$code}");
        }

        return $code;
    }

    /**
     * Vérifie un code. Incrémente le compteur de tentatives à chaque essai
     * raté et invalide le code après TENTATIVES_MAX échecs (protection
     * anti brute-force sur un espace de seulement 6 chiffres).
     */
    public static function verifierCode(string $email, string $code, string $type, string $purpose): bool
    {
        $row = DB::table('otp_codes')
            ->where('email', $email)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->first();

        if (!$row) {
            return false;
        }

        if (now()->greaterThan($row->expires_at) || $row->tentatives >= self::TENTATIVES_MAX) {
            return false;
        }

        if (Hash::check($code, $row->code)) {
            self::oublierCode($email, $type, $purpose);
            return true;
        }

        DB::table('otp_codes')->where('id', $row->id)->increment('tentatives');

        return false;
    }

    public static function oublierCode(string $email, string $type, string $purpose): void
    {
        DB::table('otp_codes')
            ->where('email', $email)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->delete();
    }
}
