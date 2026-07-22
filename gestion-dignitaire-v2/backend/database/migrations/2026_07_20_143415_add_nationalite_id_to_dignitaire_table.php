<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `nationalite` (varchar libre) reste en place pour les données existantes,
 * mais devient obsolète : la nationalité se rattache désormais à un vrai
 * pays via nationalite_id, distinct des affectations (pays où le
 * dignitaire est en poste, qui peut différer de sa nationalité).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->integer('nationalite_id')->nullable()->after('nationalite');
        });
    }

    public function down(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->dropColumn('nationalite_id');
        });
    }
};
