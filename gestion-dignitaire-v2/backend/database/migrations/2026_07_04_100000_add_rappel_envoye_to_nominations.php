<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Marqueur d'envoi du rappel d'expiration de mandat
 *
 * Évite de renvoyer l'email de rappel chaque jour tant que la nomination
 * n'est pas clôturée.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nominations', function (Blueprint $table) {
            $table->boolean('rappel_envoye')->default(false)->after('motif_fin');
        });
    }

    public function down(): void
    {
        Schema::table('nominations', function (Blueprint $table) {
            $table->dropColumn('rappel_envoye');
        });
    }
};
