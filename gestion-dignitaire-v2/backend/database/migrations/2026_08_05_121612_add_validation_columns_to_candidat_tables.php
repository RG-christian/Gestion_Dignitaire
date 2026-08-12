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
        // Ajouter colonnes de validation pour candidat_documents
        Schema::table('candidat_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('candidat_documents', 'statut_validation')) {
                $table->enum('statut_validation', ['en_attente', 'valide', 'rejete'])->default('en_attente')->after('description');
            }
            if (!Schema::hasColumn('candidat_documents', 'motif_rejet')) {
                $table->text('motif_rejet')->nullable()->after('description');
            }
            if (!Schema::hasColumn('candidat_documents', 'valide_le')) {
                $table->timestamp('valide_le')->nullable()->after('description');
            }
            if (!Schema::hasColumn('candidat_documents', 'valide_par')) {
                $table->unsignedBigInteger('valide_par')->nullable()->after('description')->comment('ID de l\'admin qui a validé/rejeté');
            }
        });

        // Ajouter colonnes de validation pour candidat_diplomes
        Schema::table('candidat_diplomes', function (Blueprint $table) {
            if (!Schema::hasColumn('candidat_diplomes', 'statut_validation')) {
                $table->enum('statut_validation', ['en_attente', 'valide', 'rejete'])->default('en_attente')->after('justificatif_path');
            }
            if (!Schema::hasColumn('candidat_diplomes', 'motif_rejet')) {
                $table->text('motif_rejet')->nullable()->after('justificatif_path');
            }
            if (!Schema::hasColumn('candidat_diplomes', 'valide_le')) {
                $table->timestamp('valide_le')->nullable()->after('justificatif_path');
            }
            if (!Schema::hasColumn('candidat_diplomes', 'valide_par')) {
                $table->unsignedBigInteger('valide_par')->nullable()->after('justificatif_path')->comment('ID de l\'admin qui a validé/rejeté');
            }
        });

        // Ajouter colonnes de validation pour candidat_experiences
        Schema::table('candidat_experiences', function (Blueprint $table) {
            if (!Schema::hasColumn('candidat_experiences', 'statut_validation')) {
                $table->enum('statut_validation', ['en_attente', 'valide', 'rejete'])->default('en_attente')->after('justificatif_path');
            }
            if (!Schema::hasColumn('candidat_experiences', 'motif_rejet')) {
                $table->text('motif_rejet')->nullable()->after('justificatif_path');
            }
            if (!Schema::hasColumn('candidat_experiences', 'valide_le')) {
                $table->timestamp('valide_le')->nullable()->after('justificatif_path');
            }
            if (!Schema::hasColumn('candidat_experiences', 'valide_par')) {
                $table->unsignedBigInteger('valide_par')->nullable()->after('justificatif_path')->comment('ID de l\'admin qui a validé/rejeté');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidat_documents', function (Blueprint $table) {
            $table->dropColumn(['statut_validation', 'motif_rejet', 'valide_le', 'valide_par']);
        });

        Schema::table('candidat_diplomes', function (Blueprint $table) {
            $table->dropColumn(['statut_validation', 'motif_rejet', 'valide_le', 'valide_par']);
        });

        Schema::table('candidat_experiences', function (Blueprint $table) {
            $table->dropColumn(['statut_validation', 'motif_rejet', 'valide_le', 'valide_par']);
        });
    }
};
