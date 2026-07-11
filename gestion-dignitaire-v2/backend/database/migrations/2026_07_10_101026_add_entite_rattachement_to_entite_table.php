<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le CR de réunion distingue deux notions pour une entité : l'entité parente
 * (hiérarchie organique, déjà couverte par id_sup) et l'entité de
 * rattachement (rattachement administratif, potentiellement différent du
 * lien hiérarchique direct).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entite', function (Blueprint $table) {
            // Type int(11) signé pour matcher entite.id / id_sup existants
            $table->integer('entite_rattachement_id')->nullable()->after('id_sup');
        });
    }

    public function down(): void
    {
        Schema::table('entite', function (Blueprint $table) {
            $table->dropColumn('entite_rattachement_id');
        });
    }
};
