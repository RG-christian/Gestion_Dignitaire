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
            // Nullable : relie une affectation générée automatiquement au
            // poste qui l'a produite (cf. PosteController). Les affectations
            // créées manuellement depuis /affectations gardent poste_id = null.
            $table->integer('poste_id')->nullable()->after('dignitaire_id');
            $table->index('poste_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
            $table->dropIndex(['poste_id']);
            $table->dropColumn('poste_id');
        });
    }
};
