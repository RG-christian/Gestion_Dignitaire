<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Réglages applicatifs clé/valeur (table `parametres`), avec cache courte
 * durée pour éviter une requête DB à chaque login. Utilisé notamment pour
 * les interrupteurs OTP à la connexion (cf. BLOC 14 du planning : "l'OTP ne
 * doit pas être imposé tant qu'il n'est pas activé opérationnellement").
 */
class Parametres
{
    public const OTP_LOGIN_ADMIN = 'otp_login_admin_enabled';
    public const OTP_LOGIN_CANDIDAT = 'otp_login_candidat_enabled';

    public static function get(string $cle, mixed $default = null): mixed
    {
        return Cache::remember("parametre:{$cle}", 60, function () use ($cle, $default) {
            $row = DB::table('parametres')->where('cle', $cle)->first();

            return $row ? $row->valeur : $default;
        });
    }

    public static function getBool(string $cle, bool $default = false): bool
    {
        $valeur = self::get($cle, $default ? '1' : '0');

        return in_array($valeur, [true, '1', 1, 'true'], true);
    }

    public static function set(string $cle, mixed $valeur): void
    {
        DB::table('parametres')->updateOrInsert(
            ['cle' => $cle],
            ['valeur' => (string) $valeur, 'updated_at' => now(), 'created_at' => now()]
        );

        Cache::forget("parametre:{$cle}");
    }
}
