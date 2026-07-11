<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Système d'ancienneté demandé en réunion : date d'acceptation/d'affectation
 * du dignitaire, utilisée comme référence pour calculer son ancienneté.
 * Si absente, l'ancienneté retombe sur la date de début du plus ancien poste
 * (voir Dignitaire::getAncienneteAttribute()).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->date('date_prise_fonction')->nullable()->after('date_naissance');
        });
    }

    public function down(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->dropColumn('date_prise_fonction');
        });
    }
};
