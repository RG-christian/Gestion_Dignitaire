<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dignitaires', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique()->nullable();
            $table->string('matricule', 20)->unique();
            $table->string('nom', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->date('date_naissance')->nullable();
            $table->foreignId('lieu_naissance')->nullable()->constrained('villes')->onDelete('set null');
            $table->string('nationalite', 100)->nullable();
            $table->string('genre', 10)->nullable();
            $table->string('etat_civil', 20)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('casier_judiciaire', 255)->nullable();
            $table->string('certificats_medicaux', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dignitaires');
    }
};
