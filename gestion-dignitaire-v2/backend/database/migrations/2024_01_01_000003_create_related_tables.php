<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Diplômes
        Schema::create('diplomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->string('intitule', 255)->nullable();
            $table->foreignId('etablissement_id')->nullable()->constrained('etablissements')->onDelete('set null');
            $table->string('annee', 10)->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->foreignId('domaine_id')->nullable()->constrained('domaines')->onDelete('set null');
            $table->string('code', 30)->nullable();
            $table->string('type', 30)->nullable();
            $table->timestamps();
        });

        // Enfants
        Schema::create('enfants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->string('nom', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->date('date_naissance')->nullable();
            $table->foreignId('lieu_naissance')->nullable()->constrained('villes')->onDelete('set null');
            $table->string('genre', 10)->nullable();
            $table->timestamps();
        });

        // Langues parlées
        Schema::create('langues_parlees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('langue_id')->constrained('langues')->onDelete('cascade');
            $table->string('niveau', 30)->nullable();
            $table->timestamps();
        });

        // Expériences
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->string('intitule', 150)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->foreignId('structure_id')->nullable()->constrained('structures')->onDelete('set null');
            $table->timestamps();
        });

        // Postes
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->string('intitule', 150)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->foreignId('entite_id')->nullable()->constrained('entites')->onDelete('set null');
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->timestamps();
        });

        // Nominations
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('entite_id')->nullable()->constrained('entites')->onDelete('set null');
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('pv_id')->nullable()->constrained('pvs')->onDelete('set null');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('fonction', 150)->nullable();
            $table->timestamps();
        });

        // Historique nominations
        Schema::create('historique_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained()->onDelete('cascade');
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('entite_id')->nullable()->constrained('entites')->onDelete('set null');
            $table->date('date_nomination')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Décorations des dignitaires
        Schema::create('decoration_dignitaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('decoration_id')->constrained()->onDelete('cascade');
            $table->date('date_attribution');
            $table->timestamps();
        });

        // Tables de liaison pour les permissions
        Schema::create('role_fonction', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'fonction_id']);
        });

        Schema::create('role_sousfonction', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'sousfonction_id']);
        });

        Schema::create('user_fonction', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'fonction_id']);
        });

        Schema::create('user_sousfonction', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'sousfonction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sousfonction');
        Schema::dropIfExists('user_fonction');
        Schema::dropIfExists('role_sousfonction');
        Schema::dropIfExists('role_fonction');
        Schema::dropIfExists('decoration_dignitaire');
        Schema::dropIfExists('historique_nominations');
        Schema::dropIfExists('nominations');
        Schema::dropIfExists('postes');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('langues_parlees');
        Schema::dropIfExists('enfants');
        Schema::dropIfExists('diplomes');
    }
};
