<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION : Rôles et permissions granulaires
 *
 * 4 rôles (Assistant, Gestionnaire, Administrateur, Super Administrateur)
 * et un niveau lecture/écriture par sous-fonction assignée, pour les rôles
 * Assistant/Gestionnaire (Administrateur/Super Administrateur ont un accès
 * complet sans passer par ce mécanisme).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Renommer le rôle existant pour coller à la nomenclature du client
        DB::table('roles')->where('role_name', 'Superadmin')->update(['role_name' => 'Super Administrateur']);

        // Ajouter les 2 rôles manquants (Assistant existe déjà)
        foreach (['Gestionnaire', 'Administrateur'] as $role) {
            if (!DB::table('roles')->where('role_name', $role)->exists()) {
                DB::table('roles')->insert(['role_name' => $role]);
            }
        }

        Schema::table('user_sousfonctions', function (Blueprint $table) {
            $table->enum('niveau', ['lecture', 'ecriture'])->default('lecture')->after('sousfonction_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_sousfonctions', function (Blueprint $table) {
            $table->dropColumn('niveau');
        });

        DB::table('roles')->whereIn('role_name', ['Gestionnaire', 'Administrateur'])->delete();
        DB::table('roles')->where('role_name', 'Super Administrateur')->update(['role_name' => 'Superadmin']);
    }
};
