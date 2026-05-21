<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pour les téléphones (plusieurs par dignitaire)
        Schema::create('dignitaire_telephones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dignitaire_id');
            $table->string('numero', 20);
            $table->string('type', 20)->default('mobile'); // mobile, fixe, bureau
            $table->boolean('principal')->default(false);
            $table->timestamps();

            // Foreign key vers la table dignitaire (sans 's')
            $table->foreign('dignitaire_id')->references('id')->on('dignitaire')->onDelete('cascade');
            $table->index('dignitaire_id');
        });

        // Table pour les emails (plusieurs par dignitaire)
        Schema::create('dignitaire_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dignitaire_id');
            $table->string('email', 255);
            $table->string('type', 20)->default('personnel'); // personnel, professionnel
            $table->boolean('principal')->default(false);
            $table->timestamps();

            // Foreign key vers la table dignitaire (sans 's')
            $table->foreign('dignitaire_id')->references('id')->on('dignitaire')->onDelete('cascade');
            $table->index('dignitaire_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dignitaire_emails');
        Schema::dropIfExists('dignitaire_telephones');
    }
};
