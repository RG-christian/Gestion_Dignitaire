<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Table des documents des candidats
 * 
 * Permet aux candidats de joindre plusieurs documents lors de leur candidature
 * (diplômes, attestations, CV, lettre de motivation, etc.)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidat_documents', function (Blueprint $table) {
            $table->id();
            
            // Relation avec le candidat
            $table->foreignId('candidat_id')->constrained('candidats')->onDelete('cascade');
            
            // Informations du document
            $table->string('type_document', 100)->comment('diplome, attestation, casier, medical, cv, lettre, autre');
            $table->string('nom_fichier', 255)->comment('Nom original du fichier');
            $table->string('chemin_fichier', 255)->comment('Chemin de stockage du fichier');
            $table->integer('taille_fichier')->nullable()->comment('Taille en octets');
            $table->string('extension', 10)->nullable()->comment('Extension du fichier (pdf, jpg, png, etc.)');
            $table->text('description')->nullable()->comment('Description optionnelle du document');
            
            // Traçabilité
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            
            // Index pour optimisation
            $table->index('candidat_id');
            $table->index('type_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidat_documents');
    }
};
