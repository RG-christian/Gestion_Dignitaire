<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La gestion des Entités n'avait jusqu'ici aucune sous-fonction
        // dédiée et était ouverte à tout utilisateur authentifié, quel que
        // soit son rôle — trou dans le système de permissions. Rattachée à
        // "Organisation" (fonction_id=7), aux côtés de Poste et Structure.
        DB::table('sousfonctions')->insertOrIgnore([
            'sousfonction_name' => 'Entité',
            'fonction_id' => 7,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('user_sousfonctions')->whereIn(
            'sousfonction_id',
            DB::table('sousfonctions')->where('sousfonction_name', 'Entité')->pluck('id')
        )->delete();
        DB::table('sousfonctions')->where('sousfonction_name', 'Entité')->delete();
    }
};
