<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidat_id');
            $table->enum('type', ['nouveau_document', 'nouveau_diplome', 'nouvelle_experience', 'modification_profil'])->default('modification_profil');
            $table->string('titre');
            $table->text('message');
            $table->boolean('lu')->default(false);
            $table->timestamp('lu_le')->nullable();
            $table->timestamps();

            $table->foreign('candidat_id')->references('id')->on('candidats')->onDelete('cascade');
            $table->index(['lu', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
