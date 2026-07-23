<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aligne le système de sous-fonctions sur ce que la sidebar affichait déjà
 * (Conjoints/Affectations/Candidatures rattachées à "Dignitaire" sans ligne
 * propre, "Rapports & Traçabilité" jamais représentée du tout comme
 * fonction) — pour que tout soit réellement attribuable module par module
 * depuis /admin/create, plutôt que certaines parties du menu échappant au
 * système au grand complet.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Conjoint et Affectation : nouvelles sous-fonctions sous "Gest.
        // Pers." (fonction_id=1), pour les détacher de "Dignitaire" et les
        // attribuer indépendamment.
        DB::table('sousfonctions')->insertOrIgnore([
            ['sousfonction_name' => 'Conjoint', 'fonction_id' => 1],
            ['sousfonction_name' => 'Affectation', 'fonction_id' => 1],
            // Candidature : créée pour la cohérence d'affichage, mais la
            // route reste verrouillée derrière admin-access en plus (accès
            // réservé aux rôles à accès complet, décision validée avec
            // l'utilisateur — données personnelles de candidats).
            ['sousfonction_name' => 'Candidature', 'fonction_id' => 1],
        ]);

        $fonctionId = DB::table('fonctions')->insertGetId([
            'fonction_name' => 'Rapports & Traçabilité',
        ]);

        DB::table('sousfonctions')->insertOrIgnore([
            ['sousfonction_name' => 'Rapports & Exports', 'fonction_id' => $fonctionId],
            ['sousfonction_name' => 'Journal des actions', 'fonction_id' => $fonctionId],
            // Paramètres (OTP) : créée pour la cohérence d'affichage, mais
            // la route reste verrouillée derrière super-admin en plus
            // (réglage de sécurité global de l'application, décision
            // validée avec l'utilisateur).
            ['sousfonction_name' => 'Paramètres (OTP)', 'fonction_id' => $fonctionId],
        ]);
    }

    public function down(): void
    {
        $noms = ['Conjoint', 'Affectation', 'Candidature', 'Rapports & Exports', 'Journal des actions', 'Paramètres (OTP)'];

        DB::table('user_sousfonctions')->whereIn(
            'sousfonction_id',
            DB::table('sousfonctions')->whereIn('sousfonction_name', $noms)->pluck('id')
        )->delete();
        DB::table('sousfonctions')->whereIn('sousfonction_name', $noms)->delete();
        DB::table('fonctions')->where('fonction_name', 'Rapports & Traçabilité')->delete();
    }
};
