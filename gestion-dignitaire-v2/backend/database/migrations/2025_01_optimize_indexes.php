<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Index pour dignitaire
        Schema::table('dignitaire', function (Blueprint $table) {
            if (!$this->indexExists('dignitaire', 'idx_lieu_naissance')) {
                $table->index('lieu_naissance', 'idx_lieu_naissance');
            }
            if (!$this->indexExists('dignitaire', 'idx_genre')) {
                $table->index('genre', 'idx_genre');
            }
            if (!$this->indexExists('dignitaire', 'idx_matricule')) {
                $table->index('matricule', 'idx_matricule');
            }
            if (!$this->indexExists('dignitaire', 'idx_nip')) {
                $table->index('nip', 'idx_nip');
            }
        });

        // Index pour postes
        Schema::table('postes', function (Blueprint $table) {
            if (!$this->indexExists('postes', 'idx_dignitaire_dates')) {
                $table->index(['dignitaire_id', 'date_debut', 'date_fin'], 'idx_dignitaire_dates');
            }
            if (!$this->indexExists('postes', 'idx_entite')) {
                $table->index('entite_id', 'idx_entite');
            }
            if (!$this->indexExists('postes', 'idx_ville')) {
                $table->index('ville_id', 'idx_ville');
            }
        });

        // Index pour nominations
        Schema::table('nomination', function (Blueprint $table) {
            if (!$this->indexExists('nomination', 'idx_dignitaire')) {
                $table->index('dignitaire_id', 'idx_dignitaire');
            }
            if (!$this->indexExists('nomination', 'idx_date')) {
                $table->index('date_nomination', 'idx_date');
            }
        });

        // Index pour decorations
        Schema::table('decoration_dignitaire', function (Blueprint $table) {
            if (!$this->indexExists('decoration_dignitaire', 'idx_dignitaire')) {
                $table->index('dignitaire_id', 'idx_dignitaire');
            }
            if (!$this->indexExists('decoration_dignitaire', 'idx_decoration')) {
                $table->index('decoration_id', 'idx_decoration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->dropIndex('idx_lieu_naissance');
            $table->dropIndex('idx_genre');
            $table->dropIndex('idx_matricule');
            $table->dropIndex('idx_nip');
        });

        Schema::table('postes', function (Blueprint $table) {
            $table->dropIndex('idx_dignitaire_dates');
            $table->dropIndex('idx_entite');
            $table->dropIndex('idx_ville');
        });

        Schema::table('nomination', function (Blueprint $table) {
            $table->dropIndex('idx_dignitaire');
            $table->dropIndex('idx_date');
        });

        Schema::table('decoration_dignitaire', function (Blueprint $table) {
            $table->dropIndex('idx_dignitaire');
            $table->dropIndex('idx_decoration');
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        return count($indexes) > 0;
    }
};
