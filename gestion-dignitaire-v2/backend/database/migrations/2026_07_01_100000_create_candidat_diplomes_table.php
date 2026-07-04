<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Diplômes déclarés par un candidat avant validation
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidat_diplomes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidat_id')->constrained('candidats')->onDelete('cascade');

            $table->string('intitule', 255)->nullable();
            $table->integer('etablissement_id')->nullable();
            $table->foreign('etablissement_id')->references('id')->on('etablissement')->nullOnDelete();
            $table->integer('ville_id')->nullable();
            $table->foreign('ville_id')->references('id')->on('ville')->nullOnDelete();
            $table->integer('domaine_id')->nullable();
            $table->foreign('domaine_id')->references('id')->on('domaine')->nullOnDelete();
            $table->string('annee', 10)->nullable();
            $table->string('justificatif_path', 255)->nullable();

            $table->timestamps();

            $table->index('candidat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidat_diplomes');
    }
};
