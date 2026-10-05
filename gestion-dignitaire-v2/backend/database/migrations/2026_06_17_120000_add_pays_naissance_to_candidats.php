<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Le dump historique utilise INT signé pour pays.id, tandis qu'une
        // installation Laravel neuve utilise BIGINT UNSIGNED via id(). La
        // colonne enfant doit reprendre exactement le type réellement présent.
        $paysId = DB::selectOne("SHOW COLUMNS FROM `pays` WHERE Field = 'id'");
        $type = strtolower((string) $paysId->Type);
        $sqlType = str_contains($type, 'bigint') ? 'BIGINT' : 'INT';
        $unsigned = str_contains($type, 'unsigned') ? ' UNSIGNED' : '';

        if (!Schema::hasColumn('candidats', 'pays_naissance_id')) {
            DB::statement("ALTER TABLE `candidats` ADD `pays_naissance_id` {$sqlType}{$unsigned} NULL AFTER `matricule`");
        } else {
            // Rend la migration réentrante après une exécution MySQL partielle.
            DB::statement("ALTER TABLE `candidats` MODIFY `pays_naissance_id` {$sqlType}{$unsigned} NULL");
        }

        if (!Schema::hasColumn('candidats', 'ville_naissance_custom')) {
            Schema::table('candidats', function (Blueprint $table) {
                $table->string('ville_naissance_custom', 100)->nullable()->after('lieu_naissance_id')
                    ->comment('Ville de naissance si non trouvée dans la liste');
            });
        }

        $foreignKeyExists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'candidats')
            ->where('COLUMN_NAME', 'pays_naissance_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if (!$foreignKeyExists) {
            Schema::table('candidats', function (Blueprint $table) {
                $table->foreign('pays_naissance_id')
                    ->references('id')
                    ->on('pays')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('candidats', 'pays_naissance_id')) {
            $foreignKeyExists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
                ->where('TABLE_NAME', 'candidats')
                ->where('COLUMN_NAME', 'pays_naissance_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->exists();

            Schema::table('candidats', function (Blueprint $table) use ($foreignKeyExists) {
                if ($foreignKeyExists) {
                    $table->dropForeign(['pays_naissance_id']);
                }
                $table->dropColumn('pays_naissance_id');
            });
        }

        if (Schema::hasColumn('candidats', 'ville_naissance_custom')) {
            Schema::table('candidats', function (Blueprint $table) {
                $table->dropColumn('ville_naissance_custom');
            });
        }
    }
};
