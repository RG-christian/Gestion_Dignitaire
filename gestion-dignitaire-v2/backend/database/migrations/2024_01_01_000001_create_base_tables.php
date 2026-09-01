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
        // TABLE DES ROLES
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 50)->unique();
        });

        // TABLE DES FONCTIONS (Menus principaux)
        Schema::create('fonctions', function (Blueprint $table) {
            $table->id();
            $table->string('fonction_name', 50)->unique();
        });

        // TABLE DES SOUS-FONCTIONS (Sous-menus)
        Schema::create('sousfonctions', function (Blueprint $table) {
            $table->id();
            $table->string('sousfonction_name', 50);
            $table->foreignId('fonction_id')->constrained('fonctions')->onDelete('cascade');
        });

        // TABLE DES UTILISATEURS
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('nom_complet', 100);
            $table->string('password', 255);
            $table->string('email', 100)->unique();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });

        // Rôle a accès à des FONCTIONS
        Schema::create('roles_fonctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained('fonctions')->onDelete('cascade');
            $table->unique(['role_id', 'fonction_id']);
        });

        // Rôle a accès à des SOUS-FONCTIONS
        Schema::create('roles_sousfonctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained('sousfonctions')->onDelete('cascade');
            $table->unique(['role_id', 'sousfonction_id']);
        });

        // User - Fonctions
        Schema::create('user_fonctions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained('fonctions')->onDelete('cascade');
            $table->primary(['user_id', 'fonction_id']);
        });

        // User - Sous-fonctions
        Schema::create('user_sousfonctions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained('sousfonctions')->onDelete('cascade');
            $table->primary(['user_id', 'sousfonction_id']);
        });

        // TABLE DOMAINE
        Schema::create('domaine', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->text('description')->nullable();
        });

        // TABLE LANGUE
        Schema::create('langue', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50)->unique();
        });

        // TABLE REGION
        Schema::create('region', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
        });

        // TABLE PAYS
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('code_iso', 3)->unique()->nullable();
            $table->string('indicatif', 10);
            $table->string('continent', 50)->nullable();
            $table->foreignId('region_id')->nullable()->constrained('region')->onDelete('set null');
        });

        // TABLE VILLE
        Schema::create('ville', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->foreignId('pays_id')->nullable()->constrained('pays')->onDelete('cascade');
        });

        // TABLE STRUCTURE
        Schema::create('structure', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->text('adresse')->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('ville')->onDelete('set null');
        });

        // TABLE ETABLISSEMENT
        Schema::create('etablissement', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('ville')->onDelete('set null');
        });

        // TABLE ENTITE
        Schema::create('entite', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->unsignedBigInteger('id_sup')->nullable();
            $table->text('description')->nullable();
        });

        // TABLE DIGNITAIRE
        Schema::create('dignitaire', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique()->nullable();
            $table->string('matricule', 20)->unique();
            $table->string('nom', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->date('date_naissance')->nullable();
            $table->foreignId('lieu_naissance')->nullable()->constrained('ville')->onDelete('set null');
            $table->string('nationalite', 100)->nullable();
            $table->string('genre', 10)->nullable();
            $table->string('etat_civil', 20)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('casierJud', 255)->nullable();
            $table->string('certificatsMed', 255)->nullable();
        });

        // TABLE DECORATION
        Schema::create('decoration', function (Blueprint $table) {
            $table->id('deco_id');
            $table->string('deco_nom', 150)->nullable();
            $table->string('deco_type', 50)->nullable();
            $table->string('deco_niveau', 50)->nullable();
            $table->string('deco_grade', 50)->nullable();
            $table->date('deco_date_obtention')->nullable();
            $table->string('deco_autorite', 50)->nullable();
            $table->string('deco_motif', 50)->nullable();
            $table->string('deco_description', 255)->nullable();
            $table->string('deco_fichierAttestation', 100)->nullable();
        });

        // TABLE PV
        Schema::create('pv', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->date('date');
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pv');
        Schema::dropIfExists('decoration');
        Schema::dropIfExists('dignitaire');
        Schema::dropIfExists('entite');
        Schema::dropIfExists('etablissement');
        Schema::dropIfExists('structure');
        Schema::dropIfExists('ville');
        Schema::dropIfExists('pays');
        Schema::dropIfExists('region');
        Schema::dropIfExists('langue');
        Schema::dropIfExists('domaine');
        Schema::dropIfExists('user_sousfonctions');
        Schema::dropIfExists('user_fonctions');
        Schema::dropIfExists('roles_sousfonctions');
        Schema::dropIfExists('roles_fonctions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sousfonctions');
        Schema::dropIfExists('fonctions');
        Schema::dropIfExists('roles');
    }
};
