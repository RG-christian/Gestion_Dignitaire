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
        // TABLE DIPLOME
        Schema::create('diplome', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->string('intitule', 255)->nullable();
            $table->foreignId('etablissement_id')->nullable()->constrained('etablissement')->onDelete('set null');
            $table->string('annee', 10)->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('ville')->onDelete('set null');
            $table->foreignId('domaine_id')->nullable()->constrained('domaine')->onDelete('set null');
            $table->string('code', 30)->nullable();
            $table->string('type', 30)->nullable();
        });

        // TABLE ENFANTS
        Schema::create('enfants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->string('nom', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->date('date_naissance')->nullable();
            $table->foreignId('lieu_naissance')->nullable()->constrained('ville')->onDelete('set null');
            $table->string('genre', 10)->nullable();
        });

        // TABLE LANGUES
        Schema::create('langues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->foreignId('langue_id')->constrained('langue')->onDelete('cascade');
            $table->string('niveau', 30)->nullable();
        });

        // TABLE EXPERIENCES
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->string('intitule', 150)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->foreignId('structure_id')->nullable()->constrained('structure')->onDelete('set null');
        });

        // TABLE POSTES
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->string('intitule', 150)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->foreignId('entite_id')->nullable()->constrained('entite')->onDelete('set null');
            $table->foreignId('ville_id')->nullable()->constrained('ville')->onDelete('set null');
        });

        // TABLE NOMINATIONS
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->foreignId('entite_id')->nullable()->constrained('entite')->onDelete('set null');
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('pv_id')->nullable()->constrained('pv')->onDelete('set null');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('fonction', 150)->nullable();
        });

        // TABLE HISTORIQUE NOMINATIONS
        Schema::create('historique_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('nominations')->onDelete('cascade');
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('entite_id')->nullable()->constrained('entite')->onDelete('set null');
            $table->date('date_nomination')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('date_modification')->useCurrent();
        });

        // TABLE DECORATION_DIGNITAIRE (Pivot)
        Schema::create('decoration_dignitaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaire')->onDelete('cascade');
            $table->unsignedBigInteger('decoration_id');
            $table->foreign('decoration_id')->references('deco_id')->on('decoration')->onDelete('cascade');
            $table->date('date_attribution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decoration_dignitaire');
        Schema::dropIfExists('historique_nominations');
        Schema::dropIfExists('nominations');
        Schema::dropIfExists('postes');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('langues');
        Schema::dropIfExists('enfants');
        Schema::dropIfExists('diplome');
    }
};
