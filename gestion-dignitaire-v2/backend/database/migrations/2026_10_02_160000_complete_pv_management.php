<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pv', function (Blueprint $table) {
            if (!Schema::hasColumn('pv', 'fichier_path')) {
                $table->string('fichier_path')->nullable()->after('description');
            }
            if (!Schema::hasColumn('pv', 'statut')) {
                $table->string('statut', 20)->default('actif')->after('fichier_path')->index();
            }
            if (!Schema::hasColumn('pv', 'archive_le')) {
                $table->dateTime('archive_le')->nullable()->after('statut');
            }
        });

        $fonctionId = DB::table('sousfonctions')
            ->where('sousfonction_name', 'Nomination')
            ->value('fonction_id')
            ?? DB::table('fonctions')->where('fonction_name', 'Parcours Pro.')->value('id');

        if ($fonctionId) {
            DB::table('sousfonctions')->insertOrIgnore([
                'sousfonction_name' => 'Procès-verbal',
                'fonction_id' => $fonctionId,
            ]);
        }
    }

    public function down(): void
    {
        $sousfonctionId = DB::table('sousfonctions')
            ->where('sousfonction_name', 'Procès-verbal')
            ->value('id');

        if ($sousfonctionId) {
            DB::table('user_sousfonctions')->where('sousfonction_id', $sousfonctionId)->delete();
            DB::table('roles_sousfonctions')->where('sousfonction_id', $sousfonctionId)->delete();
            DB::table('sousfonctions')->where('id', $sousfonctionId)->delete();
        }

        Schema::table('pv', function (Blueprint $table) {
            if (Schema::hasColumn('pv', 'statut')) {
                $table->dropIndex(['statut']);
            }
            $columns = array_filter(
                ['fichier_path', 'statut', 'archive_le'],
                fn (string $column) => Schema::hasColumn('pv', $column)
            );
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
