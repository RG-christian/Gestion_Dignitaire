<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Documents attachés à un dignitaire déjà validé (diplôme,
 * passeport, casier judiciaire, certificat médical, attestation...).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dignitaire_documents', function (Blueprint $table) {
            $table->id();

            $table->integer('dignitaire_id');
            $table->foreign('dignitaire_id')->references('id')->on('dignitaire')->cascadeOnDelete();

            $table->string('type_document', 100);
            $table->string('nom_document', 255)->nullable();
            $table->string('numero_document', 100)->nullable();
            $table->date('date_emission')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('organisme_emetteur', 255)->nullable();
            $table->text('description')->nullable();

            $table->string('nom_fichier', 255);
            $table->string('chemin_fichier', 255);
            $table->integer('taille_fichier')->nullable();
            $table->string('extension', 10)->nullable();

            $table->timestamps();

            $table->index('dignitaire_id');
            $table->index('type_document');
            $table->index('date_expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dignitaire_documents');
    }
};
