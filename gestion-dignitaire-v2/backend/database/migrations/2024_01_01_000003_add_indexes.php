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
        // Index sur les tables principales
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->index('nom', 'idx_dignitaire_nom');
            $table->index('prenom', 'idx_dignitaire_prenom');
            $table->index('matricule', 'idx_dignitaire_matricule');
            $table->index('nip', 'idx_dignitaire_nip');
        });

        // Index sur les tables de liaison
        Schema::table('diplome', function (Blueprint $table) {
            $table->index('dignitaire_id', 'idx_diplome_dignitaire');
        });

        Schema::table('enfants', function (Blueprint $table) {
            $table->index('dignitaire_id', 'idx_enfants_dignitaire');
        });

        Schema::table('langues', function (Blueprint $table) {
            $table->index('dignitaire_id', 'idx_langues_dignitaire');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->index('dignitaire_id', 'idx_experiences_dignitaire');
        });

        Schema::table('postes', function (Blueprint $table) {
            $table->index('dignitaire_id', 'idx_postes_dignitaire');
        });

        Schema::table('nominations', function (Blueprint $table) {
            $table->index('dignitaire_id', 'idx_nominations_dignitaire');
        });

        Schema::table('decoration_dignitaire', function (Blueprint $table) {
            $table->index('dignitaire_id', 'idx_decoration_dignitaire_dignitaire');
            $table->index('decoration_id', 'idx_decoration_dignitaire_decoration');
        });

        // Index sur les dates pour les recherches temporelles
        Schema::table('nominations', function (Blueprint $table) {
            $table->index('date_debut', 'idx_nominations_date_debut');
            $table->index('date_fin', 'idx_nominations_date_fin');
        });

        Schema::table('postes', function (Blueprint $table) {
            $table->index('date_debut', 'idx_postes_date_debut');
            $table->index('date_fin', 'idx_postes_date_fin');
        });

        // Index sur les tables de référence
        Schema::table('ville', function (Blueprint $table) {
            $table->index('nom', 'idx_ville_nom');
            $table->index('pays_id', 'idx_ville_pays');
        });

        Schema::table('pays', function (Blueprint $table) {
            $table->index('nom', 'idx_pays_nom');
        });

        Schema::table('region', function (Blueprint $table) {
            $table->index('nom', 'idx_region_nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->dropIndex('idx_dignitaire_nom');
            $table->dropIndex('idx_dignitaire_prenom');
            $table->dropIndex('idx_dignitaire_matricule');
            $table->dropIndex('idx_dignitaire_nip');
        });

        Schema::table('diplome', function (Blueprint $table) {
            $table->dropIndex('idx_diplome_dignitaire');
        });

        Schema::table('enfants', function (Blueprint $table) {
            $table->dropIndex('idx_enfants_dignitaire');
        });

        Schema::table('langues', function (Blueprint $table) {
            $table->dropIndex('idx_langues_dignitaire');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropIndex('idx_experiences_dignitaire');
        });

        Schema::table('postes', function (Blueprint $table) {
            $table->dropIndex('idx_postes_dignitaire');
        });

        Schema::table('nominations', function (Blueprint $table) {
            $table->dropIndex('idx_nominations_dignitaire');
        });

        Schema::table('decoration_dignitaire', function (Blueprint $table) {
            $table->dropIndex('idx_decoration_dignitaire_dignitaire');
            $table->dropIndex('idx_decoration_dignitaire_decoration');
        });

        Schema::table('nominations', function (Blueprint $table) {
            $table->dropIndex('idx_nominations_date_debut');
            $table->dropIndex('idx_nominations_date_fin');
        });

        Schema::table('postes', function (Blueprint $table) {
            $table->dropIndex('idx_postes_date_debut');
            $table->dropIndex('idx_postes_date_fin');
        });

        Schema::table('ville', function (Blueprint $table) {
            $table->dropIndex('idx_ville_nom');
            $table->dropIndex('idx_ville_pays');
        });

        Schema::table('pays', function (Blueprint $table) {
            $table->dropIndex('idx_pays_nom');
        });

        Schema::table('region', function (Blueprint $table) {
            $table->dropIndex('idx_region_nom');
        });
    }
};
