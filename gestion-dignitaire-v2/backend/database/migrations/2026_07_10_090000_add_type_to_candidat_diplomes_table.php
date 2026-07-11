<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Ajout du champ "type" (niveau du diplôme) sur candidat_diplomes,
 * pour aligner le formulaire candidat sur celui de l'admin (diplome.type).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidat_diplomes', function (Blueprint $table) {
            $table->string('type', 30)->nullable()->after('intitule');
        });
    }

    public function down(): void
    {
        Schema::table('candidat_diplomes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
