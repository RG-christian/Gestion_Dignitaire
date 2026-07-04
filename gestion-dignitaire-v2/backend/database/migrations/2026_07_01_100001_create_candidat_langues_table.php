<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Langues parlées déclarées par un candidat avant validation
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidat_langues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidat_id')->constrained('candidats')->onDelete('cascade');
            $table->integer('langue_id');
            $table->foreign('langue_id')->references('id')->on('langue')->cascadeOnDelete();
            $table->string('niveau', 30)->nullable();

            $table->timestamps();

            $table->index('candidat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidat_langues');
    }
};
