<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table partagée pour les demandes de réinitialisation de mot de passe des
 * deux guards de l'app (admin `users` et `candidats`, tables distinctes).
 * `type` désambiguïse : un même email pourrait théoriquement exister dans
 * les deux tables sans lien entre elles.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email');
            $table->string('type', 20); // 'admin' | 'candidat'
            $table->string('token'); // haché (Hash::make), jamais stocké en clair
            $table->timestamp('created_at')->nullable();

            $table->primary(['email', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
