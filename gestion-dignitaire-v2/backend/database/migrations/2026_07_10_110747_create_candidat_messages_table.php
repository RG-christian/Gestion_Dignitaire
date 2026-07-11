<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Messages/recommandations laissés par un admin sur une candidature,
 * consultables et suivis (lu/non lu) depuis le dashboard du candidat.
 * Sert aussi de journal unifié : les décisions de validation/refus y
 * sont dupliquées (type 'validation'/'refus') pour un historique complet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('candidats')->cascadeOnDelete();
            // users.id est un int(11) signé (table historique) : pas de contrainte
            // de clé étrangère typée, comme pour audit_logs.causer_id.
            $table->integer('user_id')->nullable();
            $table->string('user_label')->nullable();
            $table->enum('type', ['recommandation', 'validation', 'refus'])->default('recommandation');
            $table->text('contenu');
            $table->boolean('lu')->default(false);
            $table->timestamp('lu_le')->nullable();
            $table->timestamps();

            $table->index(['candidat_id', 'lu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidat_messages');
    }
};
