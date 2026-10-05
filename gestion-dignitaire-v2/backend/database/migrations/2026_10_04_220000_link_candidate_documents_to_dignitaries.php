<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dignitaire_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('source_candidat_document_id')
                ->nullable()
                ->unique()
                ->after('dignitaire_id');
            $table->foreign('source_candidat_document_id')
                ->references('id')
                ->on('candidat_documents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dignitaire_documents', function (Blueprint $table) {
            $table->dropForeign(['source_candidat_document_id']);
            $table->dropUnique(['source_candidat_document_id']);
            $table->dropColumn('source_candidat_document_id');
        });
    }
};
