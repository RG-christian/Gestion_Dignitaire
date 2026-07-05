<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Corrige le désalignement de type sur dignitaire_id
 *
 * dignitaire_emails.dignitaire_id et dignitaire_telephones.dignitaire_id
 * étaient en bigint(20) unsigned alors que dignitaire.id est en int(11)
 * signé, sans aucune contrainte de clé étrangère réelle (juste un index,
 * voire rien du tout pour telephones). On aligne le type puis on ajoute
 * la vraie contrainte, cohérent avec les autres tables du projet
 * (postes, nominations, diplome...) qui référencent dignitaire en cascade.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dignitaire_emails', function (Blueprint $table) {
            $table->integer('dignitaire_id')->unsigned(false)->change();
            $table->foreign('dignitaire_id')->references('id')->on('dignitaire')->cascadeOnDelete();
        });

        Schema::table('dignitaire_telephones', function (Blueprint $table) {
            $table->integer('dignitaire_id')->unsigned(false)->change();
            $table->foreign('dignitaire_id')->references('id')->on('dignitaire')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dignitaire_emails', function (Blueprint $table) {
            $table->dropForeign(['dignitaire_id']);
            $table->unsignedBigInteger('dignitaire_id')->change();
        });

        Schema::table('dignitaire_telephones', function (Blueprint $table) {
            $table->dropForeign(['dignitaire_id']);
            $table->unsignedBigInteger('dignitaire_id')->change();
        });
    }
};
