<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->boolean('est_militaire')->default(false)->after('etat_civil')->index();
            $table->string('grade_militaire', 100)->nullable()->after('est_militaire');
        });
    }

    public function down(): void
    {
        Schema::table('dignitaire', function (Blueprint $table) {
            $table->dropIndex(['est_militaire']);
            $table->dropColumn(['est_militaire', 'grade_militaire']);
        });
    }
};
