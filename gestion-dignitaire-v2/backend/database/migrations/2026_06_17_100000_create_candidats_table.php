<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Table des candidats (préinscription)
 * 
 * Cette table sert de zone tampon pour les candidatures avant validation définitive.
 * Un candidat validé sera converti en dignitaire.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            
            // Statut de la candidature
            $table->enum('statut', ['en_attente', 'valide', 'refuse'])->default('en_attente');
            $table->text('motif_refus')->nullable()->comment('Raison du refus si statut = refuse');
            
            // Informations personnelles (obligatoires)
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance');
            $table->enum('genre', ['M', 'F']);
            
            // Informations personnelles (optionnelles)
            $table->string('nip', 50)->nullable()->unique();
            $table->string('matricule', 50)->nullable()->unique();
            $table->integer('lieu_naissance_id')->nullable();
            $table->foreign('lieu_naissance_id')->references('id')->on('ville')->nullOnDelete();
            $table->string('etat_civil', 50)->nullable();
            
            // Photo et documents
            $table->string('photo', 255)->nullable();
            $table->string('cv_path', 255)->nullable();
            $table->string('lettre_motivation_path', 255)->nullable();
            
            // Coordonnées
            $table->string('email', 150)->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->integer('ville_residence_id')->nullable();
            $table->foreign('ville_residence_id')->references('id')->on('ville')->nullOnDelete();
            
            // Authentification candidat (pour se connecter et voir son statut)
            $table->string('password')->comment('Mot de passe haché pour connexion candidat');
            $table->rememberToken();
            
            // Traçabilité
            $table->timestamp('date_candidature')->useCurrent();
            $table->integer('valide_par')->nullable()->comment('Admin qui a validé/refusé la candidature');
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('date_validation')->nullable();
            $table->integer('dignitaire_id')->nullable()->comment('Lien vers le dignitaire créé après validation');
            $table->foreign('dignitaire_id')->references('id')->on('dignitaire')->nullOnDelete();
            
            $table->timestamps();
            
            // Index pour optimisation
            $table->index('statut');
            $table->index('email');
            $table->index('date_candidature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidats');
    }
};
