<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Journal des actions (traçabilité)
 *
 * Table générique pour tracer les créations/modifications/suppressions
 * sur les entités métier. Pas de contrainte de clé étrangère sur
 * causer_id/auditable_id : ces colonnes doivent pouvoir référencer
 * indifféremment des tables aux types de PK incompatibles
 * (users.id en int(11) signé, candidats.id en bigint unsigned...).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->string('causer_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_label')->nullable();

            $table->string('action', 30);

            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('auditable_label')->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('causer_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
