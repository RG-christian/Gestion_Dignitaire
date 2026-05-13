<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 50)->unique();
            $table->timestamps();
        });

        // Fonctions
        Schema::create('fonctions', function (Blueprint $table) {
            $table->id();
            $table->string('fonction_name', 50)->unique();
            $table->timestamps();
        });

        // Sous-fonctions
        Schema::create('sousfonctions', function (Blueprint $table) {
            $table->id();
            $table->string('sousfonction_name', 50);
            $table->foreignId('fonction_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('nom_complet', 100);
            $table->string('password');
            $table->string('email', 100)->unique();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });

        // Table pivot: user_fonctions
        Schema::create('user_fonctions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained('fonctions')->onDelete('cascade');
            $table->primary(['user_id', 'fonction_id']);
        });

        // Table pivot: user_sousfonctions
        Schema::create('user_sousfonctions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained('sousfonctions')->onDelete('cascade');
            $table->primary(['user_id', 'sousfonction_id']);
        });

        // Domaines
        Schema::create('domaines', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Langues
        Schema::create('langues', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50)->unique();
            $table->timestamps();
        });

        // Régions
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->timestamps();
        });

        // Pays
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('code_iso', 3)->unique()->nullable();
            $table->string('indicatif', 10);
            $table->string('continent', 50)->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // Villes
        Schema::create('villes', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->foreignId('pays_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Structures
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->text('adresse')->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->timestamps();
        });

        // Établissements
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->timestamps();
        });

        // Entités
        Schema::create('entites', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->foreignId('id_sup')->nullable()->constrained('entites')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // PV
        Schema::create('pvs', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Décorations
        Schema::create('decorations', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('type', 50)->nullable();
            $table->string('niveau', 50)->nullable();
            $table->string('grade', 50)->nullable();
            $table->date('date_obtention')->nullable();
            $table->string('autorite', 50)->nullable();
            $table->string('motif', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('fichier_attestation', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decorations');
        Schema::dropIfExists('pvs');
        Schema::dropIfExists('entites');
        Schema::dropIfExists('etablissements');
        Schema::dropIfExists('structures');
        Schema::dropIfExists('villes');
        Schema::dropIfExists('pays');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('langues');
        Schema::dropIfExists('domaines');
        Schema::dropIfExists('user_sousfonctions');
        Schema::dropIfExists('user_fonctions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sousfonctions');
        Schema::dropIfExists('fonctions');
        Schema::dropIfExists('roles');
    }
};
