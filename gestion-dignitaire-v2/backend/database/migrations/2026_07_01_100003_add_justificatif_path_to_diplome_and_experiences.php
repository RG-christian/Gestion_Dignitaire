<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Ajoute la possibilité de joindre un document justificatif
 * à un diplôme ou une expérience professionnelle du dignitaire
 * (repris du candidat lors de la validation de sa candidature).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diplome', function (Blueprint $table) {
            $table->string('justificatif_path', 255)->nullable()->after('type');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->string('justificatif_path', 255)->nullable()->after('structure_id');
        });
    }

    public function down(): void
    {
        Schema::table('diplome', function (Blueprint $table) {
            $table->dropColumn('justificatif_path');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('justificatif_path');
        });
    }
};
