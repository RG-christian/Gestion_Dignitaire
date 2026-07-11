<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Champs de preuve de nomination (document/image téléversé),
 * type de nomination et autorité nominatrice.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nominations', function (Blueprint $table) {
            $table->string('type_nomination', 30)->nullable()->after('fonction');
            $table->string('autorite_nominatrice', 255)->nullable()->after('type_nomination');
            $table->string('document_nomination_path', 255)->nullable()->after('numero_decret');
        });
    }

    public function down(): void
    {
        Schema::table('nominations', function (Blueprint $table) {
            $table->dropColumn(['type_nomination', 'autorite_nominatrice', 'document_nomination_path']);
        });
    }
};
