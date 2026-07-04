<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Statut explicite et motif de fin sur nominations/postes
 *
 * Distingue "fin de fonction" et "mise à disposition de l'administration
 * d'origine" (demande client), et remplace le simple `date_fin IS NULL`
 * par un vrai champ statut ('en_cours' / 'terminee').
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nominations', function (Blueprint $table) {
            $table->enum('statut', ['en_cours', 'terminee'])->default('en_cours')->after('date_fin');
            $table->enum('motif_fin', ['fin_fonction', 'mise_a_disposition'])->nullable()->after('statut');
            $table->string('numero_decret', 100)->nullable()->after('fonction');
        });

        Schema::table('postes', function (Blueprint $table) {
            $table->enum('statut', ['en_cours', 'terminee'])->default('en_cours')->after('date_fin');
            $table->enum('motif_fin', ['fin_fonction', 'mise_a_disposition'])->nullable()->after('statut');
        });

        // Backfill des lignes existantes à partir de date_fin
        DB::table('nominations')->whereNull('date_fin')->update(['statut' => 'en_cours']);
        DB::table('nominations')->whereNotNull('date_fin')->update(['statut' => 'terminee', 'motif_fin' => 'fin_fonction']);

        DB::table('postes')->whereNull('date_fin')->update(['statut' => 'en_cours']);
        DB::table('postes')->whereNotNull('date_fin')->update(['statut' => 'terminee', 'motif_fin' => 'fin_fonction']);
    }

    public function down(): void
    {
        Schema::table('nominations', function (Blueprint $table) {
            $table->dropColumn(['statut', 'motif_fin', 'numero_decret']);
        });

        Schema::table('postes', function (Blueprint $table) {
            $table->dropColumn(['statut', 'motif_fin']);
        });
    }
};
