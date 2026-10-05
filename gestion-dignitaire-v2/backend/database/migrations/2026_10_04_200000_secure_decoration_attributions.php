<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'poste_id' => fn (Blueprint $table) => $table->integer('poste_id')->nullable()->after('date_attribution'),
            'attestation_path' => fn (Blueprint $table) => $table->string('attestation_path')->nullable()->after('poste_id'),
            'attestation_nom_original' => fn (Blueprint $table) => $table->string('attestation_nom_original')->nullable()->after('attestation_path'),
            'attestation_mime' => fn (Blueprint $table) => $table->string('attestation_mime', 100)->nullable()->after('attestation_nom_original'),
            'attestation_taille' => fn (Blueprint $table) => $table->unsignedBigInteger('attestation_taille')->nullable()->after('attestation_mime'),
            'attestation_sha256' => fn (Blueprint $table) => $table->char('attestation_sha256', 64)->nullable()->after('attestation_taille'),
            'attestation_legacy' => fn (Blueprint $table) => $table->string('attestation_legacy')->nullable()->after('attestation_sha256'),
        ];

        foreach ($columns as $name => $definition) {
            if (!Schema::hasColumn('decoration_dignitaire', $name)) {
                Schema::table('decoration_dignitaire', $definition);
            }
        }

        // Le dump historique utilise un INT signé pour postes.id.
        DB::statement('ALTER TABLE decoration_dignitaire MODIFY poste_id INT NULL');

        $foreignExists = DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'decoration_dignitaire')
            ->where('constraint_name', 'decoration_dignitaire_poste_id_foreign')
            ->exists();

        if (!$foreignExists) {
            Schema::table('decoration_dignitaire', function (Blueprint $table) {
                $table->foreign('poste_id')->references('id')->on('postes')->nullOnDelete();
            });
        }

        $indexExists = collect(DB::select('SHOW INDEX FROM decoration_dignitaire'))
            ->contains(fn ($index) => $index->Key_name === 'idx_decoration_attribution_poste');
        if (!$indexExists) {
            Schema::table('decoration_dignitaire', function (Blueprint $table) {
                $table->index('poste_id', 'idx_decoration_attribution_poste');
            });
        }

        DB::table('decoration_dignitaire as dd')
            ->join('decoration as d', 'd.deco_id', '=', 'dd.decoration_id')
            ->whereNotNull('d.deco_fichierAttestation')
            ->update(['dd.attestation_legacy' => DB::raw('d.deco_fichierAttestation')]);
    }

    public function down(): void
    {
        Schema::table('decoration_dignitaire', function (Blueprint $table) {
            $table->dropForeign(['poste_id']);
            $table->dropIndex('idx_decoration_attribution_poste');
            $table->dropColumn([
                'poste_id', 'attestation_path', 'attestation_nom_original', 'attestation_mime',
                'attestation_taille', 'attestation_sha256', 'attestation_legacy',
            ]);
        });
    }
};
