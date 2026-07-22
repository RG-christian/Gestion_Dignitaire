<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Codes OTP (6 chiffres) pour la vérification d'email obligatoire à
 * l'inscription candidat, et pour la double authentification optionnelle à
 * la connexion (admin et candidat, activable indépendamment par le Super
 * Administrateur — cf. table `parametres`).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('type', 20); // 'admin' | 'candidat'
            $table->string('purpose', 20); // 'inscription' | 'connexion'
            $table->string('code'); // haché (Hash::make)
            $table->unsignedTinyInteger('tentatives')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['email', 'type', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
