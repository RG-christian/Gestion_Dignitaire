<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
            // Distingue l'affectation "principale" (le poste actuel du
            // dignitaire) d'une "mission temporaire" ponctuelle ailleurs,
            // qui peuvent coexister pour un même dignitaire.
            $table->enum('nature', ['principale', 'mission_temporaire'])->default('principale')->after('type_affectation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
            $table->dropColumn('nature');
        });
    }
};
