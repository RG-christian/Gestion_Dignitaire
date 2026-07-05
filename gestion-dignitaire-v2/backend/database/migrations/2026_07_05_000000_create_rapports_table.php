<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Archive des rapports périodiques générés automatiquement
 * (mensuel/trimestriel/annuel) par la commande `rapports:generer`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // mensuel | trimestriel | annuel
            $table->date('periode_debut');
            $table->date('periode_fin');
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->unsignedBigInteger('taille_octets')->nullable();
            $table->timestamp('genere_le');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};
