<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Coordonnées de contact et logo pour les entités (organisations).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entite', function (Blueprint $table) {
            $table->string('logo', 255)->nullable()->after('description');
            $table->string('telephone', 20)->nullable()->after('logo');
            $table->string('email', 150)->nullable()->after('telephone');
            $table->string('site_web', 255)->nullable()->after('email');
            $table->text('adresse')->nullable()->after('site_web');
        });
    }

    public function down(): void
    {
        Schema::table('entite', function (Blueprint $table) {
            $table->dropColumn(['logo', 'telephone', 'email', 'site_web', 'adresse']);
        });
    }
};
