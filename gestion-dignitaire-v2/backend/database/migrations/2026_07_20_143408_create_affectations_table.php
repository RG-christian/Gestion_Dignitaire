<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Distinction nationalité / pays d'affectation (CR de réunion, BLOC 9) :
 * une affectation est un séjour dans un pays (ambassade, mission,
 * consulat...), distincte du poste occupé (fonction/entité) et de la
 * nationalité d'origine du dignitaire.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            // Type int(11) signé pour matcher dignitaire.id / pays.id / ville.id existants
            $table->integer('dignitaire_id');
            $table->integer('pays_id');
            $table->integer('ville_id')->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('type_affectation', 100)->nullable();
            $table->enum('statut', ['en_cours', 'terminee'])->default('en_cours');
            $table->timestamps();

            $table->index('dignitaire_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
