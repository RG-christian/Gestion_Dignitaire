<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Expériences professionnelles déclarées par un candidat avant validation
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidat_experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidat_id')->constrained('candidats')->onDelete('cascade');

            $table->string('intitule', 150)->nullable();
            $table->integer('structure_id')->nullable();
            $table->foreign('structure_id')->references('id')->on('structure')->nullOnDelete();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('justificatif_path', 255)->nullable();

            $table->timestamps();

            $table->index('candidat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidat_experiences');
    }
};
