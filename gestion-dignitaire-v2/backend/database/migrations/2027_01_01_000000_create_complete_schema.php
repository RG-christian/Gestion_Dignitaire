<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION UNIQUE COMPLÈTE - Gestion des Dignitaires
 *
 * Cette migration contient TOUTE la structure de la base de données en un seul fichier.
 * Elle remplace toutes les migrations précédentes.
 *
 * ⚠️ IMPORTANT : Cette migration est conçue pour les NOUVEAUX déploiements uniquement.
 * Si votre base de données existe déjà, NE PAS exécuter cette migration.
 *
 * Contenu :
 * - Tables Laravel (users, password_resets, etc.)
 * - Tables de base (roles, fonctions, domaines, langues, régions, pays, villes)
 * - Tables principales (dignitaires, structures, établissements, entités)
 * - Tables relationnelles (diplômes, enfants, expériences, postes, nominations, décorations)
 * - Tables de contact (téléphones, emails)
 * - Index optimisés
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ============================================
        // TABLES LARAVEL (Authentification & Système)
        // ============================================

        // Users (Laravel)
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Password Reset Tokens
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Failed Jobs
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // Personal Access Tokens (Sanctum)
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }


        // ============================================
        // TABLES DE BASE (Référentiels)
        // ============================================

        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 50)->unique();
            $table->timestamps();
        });

        // Fonctions
        Schema::create('fonctions', function (Blueprint $table) {
            $table->id();
            $table->string('fonction_name', 50)->unique();
            $table->timestamps();
        });

        // Sous-fonctions
        Schema::create('sousfonctions', function (Blueprint $table) {
            $table->id();
            $table->string('sousfonction_name', 50);
            $table->foreignId('fonction_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Domaines
        Schema::create('domaines', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Langues
        Schema::create('langues', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50)->unique();
            $table->timestamps();
        });

        // Régions (avec support région/province)
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('continent', 100)->nullable(); // Pour les régions
            $table->string('type', 20)->default('region'); // 'region' ou 'province'
            $table->string('pays_nom', 100)->nullable(); // Pour les provinces
            $table->timestamps();

            $table->index('continent');
            $table->index('type');
            $table->index('pays_nom');
        });

        // Pays
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('code_iso', 3)->unique()->nullable();
            $table->string('indicatif', 10);
            $table->string('continent', 50)->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->onDelete('set null');
            $table->timestamps();
        });

        // Villes
        Schema::create('villes', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->foreignId('pays_id')->nullable()->constrained('pays')->onDelete('cascade');
            $table->integer('region_id')->nullable();
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
            $table->index('region_id');
        });

        // Structures
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->text('adresse')->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->timestamps();
        });

        // Établissements
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->timestamps();
        });

        // Entités
        Schema::create('entites', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('type', 50)->nullable();
            $table->foreignId('id_sup')->nullable()->constrained('entites')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // PV
        Schema::create('pvs', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Décorations
        Schema::create('decorations', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('type', 50)->nullable();
            $table->string('niveau', 50)->nullable();
            $table->string('grade', 50)->nullable();
            $table->date('date_obtention')->nullable();
            $table->string('autorite', 50)->nullable();
            $table->string('motif', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('fichier_attestation', 100)->nullable();
            $table->timestamps();
        });


        // ============================================
        // TABLE PRINCIPALE - DIGNITAIRES
        // ============================================

        Schema::create('dignitaires', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique()->nullable();
            $table->string('matricule', 20)->unique();
            $table->string('nom', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->date('date_naissance')->nullable();
            $table->foreignId('lieu_naissance')->nullable()->constrained('villes')->onDelete('set null');
            $table->string('nationalite', 100)->nullable();
            $table->string('genre', 10)->nullable();
            $table->string('etat_civil', 20)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('casier_judiciaire', 255)->nullable();
            $table->string('certificats_medicaux', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index optimisés
            $table->index('lieu_naissance');
            $table->index('genre');
            $table->index('matricule');
            $table->index('nip');
        });

        // ============================================
        // TABLES DE CONTACT (Téléphones & Emails)
        // ============================================

        // Téléphones
        Schema::create('dignitaire_telephones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->string('numero', 20);
            $table->string('type', 20)->default('mobile'); // mobile, fixe, bureau
            $table->boolean('principal')->default(false);
            $table->timestamps();

            $table->index('dignitaire_id');
        });

        // Emails
        Schema::create('dignitaire_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->string('email', 255);
            $table->string('type', 20)->default('personnel'); // personnel, professionnel
            $table->boolean('principal')->default(false);
            $table->timestamps();

            $table->index('dignitaire_id');
        });

        // ============================================
        // TABLES RELATIONNELLES
        // ============================================

        // Diplômes
        Schema::create('diplomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->string('intitule', 255)->nullable();
            $table->foreignId('etablissement_id')->nullable()->constrained('etablissements')->onDelete('set null');
            $table->string('annee', 10)->nullable();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->foreignId('domaine_id')->nullable()->constrained('domaines')->onDelete('set null');
            $table->string('code', 30)->nullable();
            $table->string('type', 30)->nullable();
            $table->timestamps();
        });

        // Enfants
        Schema::create('enfants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->string('nom', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->date('date_naissance')->nullable();
            $table->foreignId('lieu_naissance')->nullable()->constrained('villes')->onDelete('set null');
            $table->string('genre', 10)->nullable();
            $table->timestamps();
        });

        // Langues parlées
        Schema::create('langues_parlees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->foreignId('langue_id')->constrained('langues')->onDelete('cascade');
            $table->string('niveau', 30)->nullable();
            $table->timestamps();
        });

        // Expériences
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->string('intitule', 150)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->foreignId('structure_id')->nullable()->constrained('structures')->onDelete('set null');
            $table->timestamps();
        });

        // Postes
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->string('intitule', 150)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->foreignId('entite_id')->nullable()->constrained('entites')->onDelete('set null');
            $table->foreignId('ville_id')->nullable()->constrained('villes')->onDelete('set null');
            $table->timestamps();

            // Index optimisés
            $table->index(['dignitaire_id', 'date_debut', 'date_fin']);
            $table->index('entite_id');
            $table->index('ville_id');
        });

        // Nominations
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->foreignId('entite_id')->nullable()->constrained('entites')->onDelete('set null');
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('pv_id')->nullable()->constrained('pvs')->onDelete('set null');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('fonction', 150)->nullable();
            $table->timestamps();

            // Index optimisés
            $table->index('dignitaire_id');
            $table->index('date_debut');
        });

        // Historique nominations
        Schema::create('historique_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('nominations')->onDelete('cascade');
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('set null');
            $table->foreignId('entite_id')->nullable()->constrained('entites')->onDelete('set null');
            $table->date('date_nomination')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Décorations des dignitaires
        Schema::create('decoration_dignitaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dignitaire_id')->constrained('dignitaires')->onDelete('cascade');
            $table->foreignId('decoration_id')->constrained('decorations')->onDelete('cascade');
            $table->date('date_attribution');
            $table->timestamps();

            // Index optimisés
            $table->index('dignitaire_id');
            $table->index('decoration_id');
        });


        // ============================================
        // TABLES PIVOT (Permissions & Relations)
        // ============================================

        // User - Fonctions
        Schema::create('user_fonctions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained('fonctions')->onDelete('cascade');
            $table->primary(['user_id', 'fonction_id']);
        });

        // User - Sous-fonctions
        Schema::create('user_sousfonctions', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained('sousfonctions')->onDelete('cascade');
            $table->primary(['user_id', 'sousfonction_id']);
        });

        // Role - Fonction
        Schema::create('role_fonction', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained('fonctions')->onDelete('cascade');
            $table->primary(['role_id', 'fonction_id']);
        });

        // Role - Sous-fonction
        Schema::create('role_sousfonction', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained('sousfonctions')->onDelete('cascade');
            $table->primary(['role_id', 'sousfonction_id']);
        });

        // User - Fonction (alternative)
        Schema::create('user_fonction', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fonction_id')->constrained('fonctions')->onDelete('cascade');
            $table->primary(['user_id', 'fonction_id']);
        });

        // User - Sous-fonction (alternative)
        Schema::create('user_sousfonction', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sousfonction_id')->constrained('sousfonctions')->onDelete('cascade');
            $table->primary(['user_id', 'sousfonction_id']);
        });

        echo "\n✅ Migration complète terminée avec succès !\n";
        echo "📊 Tables créées : " . count(Schema::getAllTables()) . "\n\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer dans l'ordre inverse pour respecter les foreign keys

        // Tables pivot
        Schema::dropIfExists('user_sousfonction');
        Schema::dropIfExists('user_fonction');
        Schema::dropIfExists('role_sousfonction');
        Schema::dropIfExists('role_fonction');
        Schema::dropIfExists('user_sousfonctions');
        Schema::dropIfExists('user_fonctions');

        // Tables relationnelles
        Schema::dropIfExists('decoration_dignitaire');
        Schema::dropIfExists('historique_nominations');
        Schema::dropIfExists('nominations');
        Schema::dropIfExists('postes');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('langues_parlees');
        Schema::dropIfExists('enfants');
        Schema::dropIfExists('diplomes');

        // Tables de contact
        Schema::dropIfExists('dignitaire_emails');
        Schema::dropIfExists('dignitaire_telephones');

        // Table principale
        Schema::dropIfExists('dignitaires');

        // Tables de base
        Schema::dropIfExists('decorations');
        Schema::dropIfExists('pvs');
        Schema::dropIfExists('entites');
        Schema::dropIfExists('etablissements');
        Schema::dropIfExists('structures');
        Schema::dropIfExists('villes');
        Schema::dropIfExists('pays');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('langues');
        Schema::dropIfExists('domaines');
        Schema::dropIfExists('sousfonctions');
        Schema::dropIfExists('fonctions');
        Schema::dropIfExists('roles');

        // Tables Laravel
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        echo "\n✅ Rollback terminé - Toutes les tables ont été supprimées\n\n";
    }
};
