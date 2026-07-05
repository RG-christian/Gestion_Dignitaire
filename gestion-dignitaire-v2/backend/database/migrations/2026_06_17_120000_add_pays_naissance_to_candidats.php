<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Ajout des champs pays_naissance_id et ville_naissance_custom à la table candidats
 * 
 * Permet aux candidats de sélectionner un pays de naissance et d'entrer une ville custom
 * si leur ville n'est pas dans la liste.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidats', function (Blueprint $table) {
            // Ajout du pays de naissance
            $table->integer('pays_naissance_id')->nullable()->after('matricule');
            $table->foreign('pays_naissance_id')->references('id')->on('pays')->nullOnDelete();
            
            // Ajout de la ville custom si la ville n'existe pas dans la base
            $table->string('ville_naissance_custom', 100)->nullable()->after('lieu_naissance_id')
                ->comment('Ville de naissance si non trouvée dans la liste');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->dropForeign(['pays_naissance_id']);
            $table->dropColumn(['pays_naissance_id', 'ville_naissance_custom']);
        });
    }
};
