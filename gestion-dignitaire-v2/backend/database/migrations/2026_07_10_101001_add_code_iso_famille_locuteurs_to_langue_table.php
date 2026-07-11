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
        Schema::table('langue', function (Blueprint $table) {
            $table->string('code_iso', 10)->nullable()->after('nom');
            $table->string('famille', 100)->nullable()->after('code_iso');
            $table->string('nb_locuteurs', 50)->nullable()->after('famille');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('langue', function (Blueprint $table) {
            $table->dropColumn(['code_iso', 'famille', 'nb_locuteurs']);
        });
    }
};
