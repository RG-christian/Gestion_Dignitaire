<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Résout un ville_id à partir soit d'un ID existant, soit d'un nom de ville
 * personnalisé saisi par l'utilisateur quand sa ville n'est pas dans la
 * liste (cf. le sélecteur pays → ville avec option "Ma ville n'est pas
 * dans la liste"). Réutilise une ville existante du même pays si le nom
 * correspond déjà, pour éviter les doublons.
 */
class VilleResolver
{
    public static function resoudre(?int $villeId, ?string $nomPersonnalise, ?int $paysId): ?int
    {
        if ($villeId) {
            return $villeId;
        }

        $nom = trim((string) $nomPersonnalise);
        if ($nom === '' || !$paysId) {
            return null;
        }

        $existante = DB::table('ville')
            ->whereRaw('LOWER(nom) = ?', [mb_strtolower($nom)])
            ->where('pays_id', $paysId)
            ->first();

        if ($existante) {
            return $existante->id;
        }

        return DB::table('ville')->insertGetId([
            'nom' => $nom,
            'pays_id' => $paysId,
        ]);
    }
}
