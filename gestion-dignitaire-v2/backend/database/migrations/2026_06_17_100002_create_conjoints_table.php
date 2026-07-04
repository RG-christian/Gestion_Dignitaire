<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Table des conjoints
 * 
 * Permet d'associer un ou plusieurs conjoints à un dignitaire
 * avec lien au statut militaire/dignitaire (recommandation Marcel)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conjoints', function (Blueprint $table) {
            $table->id();
            
            // Relation avec le dignitaire
            $table->integer('dignitaire_id');
            $table->foreign('dignitaire_id')->references('id')->on('dignitaire')->cascadeOnDelete();
            
            // Informations personnelles (obligatoires)
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance')->nullable();
            $table->enum('genre', ['M', 'F']);
            
            // Informations complémentaires
            $table->integer('lieu_naissance_id')->nullable();
            $table->foreign('lieu_naissance_id')->references('id')->on('ville')->nullOnDelete();
            $table->integer('nationalite_id')->nullable();
            $table->foreign('nationalite_id')->references('id')->on('pays')->nullOnDelete();
            $table->string('profession', 255)->nullable();
            $table->string('employeur', 255)->nullable();
            
            // Informations maritales
            $table->date('date_mariage')->nullable();
            $table->string('lieu_mariage', 255)->nullable();
            $table->enum('statut', ['actif', 'divorce', 'veuf', 'separe'])->default('actif');
            $table->date('date_fin_union')->nullable()->comment('Date du divorce, décès ou séparation');
            
            // Statut spécial (recommandation Marcel)
            $table->boolean('est_militaire')->default(false);
            $table->boolean('est_dignitaire')->default(false);
            $table->string('grade_militaire', 100)->nullable();
            $table->string('fonction_dignitaire', 100)->nullable();
            
            // Coordonnées
            $table->string('telephone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('adresse')->nullable();
            
            // Documents
            $table->string('photo', 255)->nullable();
            $table->string('acte_mariage_path', 255)->nullable();
            
            // Traçabilité
            $table->timestamps();
            
            // Index pour optimisation
            $table->index('dignitaire_id');
            $table->index('statut');
            $table->index(['est_militaire', 'est_dignitaire']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conjoints');
    }
};
