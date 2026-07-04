<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Statut du dignitaire (actif / retraité / non localisé)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->enum('statut', ['actif', 'retraite', 'non_localise'])->default('actif')->after('certificatsMed');
        });
    }

    public function down(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};
