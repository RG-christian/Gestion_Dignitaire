<?php

/**
 * Script de vérification du schéma de la base de données
 *
 * Ce script vérifie que toutes les colonnes et tables nécessaires existent
 * et affiche un rapport détaillé.
 *
 * Usage: php verify_schema.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  Vérification du Schéma - Gestion des Dignitaires         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errors = [];
$warnings = [];
$success = [];

// Vérifications à effectuer
$checks = [
    'ville' => [
        'columns' => ['id', 'nom', 'pays_id', 'region_id'],
        'description' => 'Table des villes'
    ],
    'region' => [
        'columns' => ['id', 'nom', 'continent'],
        'description' => 'Table des régions'
    ],
    'pays' => [
        'columns' => ['id', 'nom', 'code_iso', 'indicatif', 'continent', 'region_id'],
        'description' => 'Table des pays'
    ],
    'dignitaire' => [
        'columns' => ['id', 'nom', 'prenom', 'genre', 'date_naissance'],
        'description' => 'Table des dignitaires'
    ]
];

// Vérifier chaque table
foreach ($checks as $table => $config) {
    echo "📋 Vérification de la table '{$table}' - {$config['description']}\n";

    if (!Schema::hasTable($table)) {
        $errors[] = "❌ La table '{$table}' n'existe pas";
        echo "   ❌ Table manquante\n";
        continue;
    }

    $success[] = "✓ Table '{$table}' existe";
    echo "   ✓ Table existe\n";

    // Vérifier les colonnes
    foreach ($config['columns'] as $column) {
        if (!Schema::hasColumn($table, $column)) {
            $errors[] = "❌ La colonne '{$table}.{$column}' n'existe pas";
            echo "   ❌ Colonne '{$column}' manquante\n";
        } else {
            echo "   ✓ Colonne '{$column}' existe\n";
        }
    }

    echo "\n";
}

// Vérifier les foreign keys
echo "🔗 Vérification des foreign keys\n";

$foreignKeys = [
    'ville' => [
        ['column' => 'region_id', 'references' => 'region.id', 'name' => 'ville_region_id_foreign']
    ]
];

foreach ($foreignKeys as $table => $keys) {
    foreach ($keys as $fk) {
        $result = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$table}'
            AND COLUMN_NAME = '{$fk['column']}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (count($result) > 0) {
            echo "   ✓ Foreign key '{$table}.{$fk['column']}' → {$fk['references']}\n";
        } else {
            $warnings[] = "⚠️  Foreign key '{$table}.{$fk['column']}' → {$fk['references']} manquante";
            echo "   ⚠️  Foreign key '{$table}.{$fk['column']}' manquante\n";
        }
    }
}

echo "\n";

// Vérifier les index
echo "📊 Vérification des index\n";

$indexes = [
    'region' => ['continent'],
    'ville' => ['region_id']
];

foreach ($indexes as $table => $columns) {
    foreach ($columns as $column) {
        $result = DB::select("
            SHOW INDEX FROM {$table} WHERE Column_name = '{$column}'
        ");

        if (count($result) > 0) {
            echo "   ✓ Index sur '{$table}.{$column}'\n";
        } else {
            $warnings[] = "⚠️  Index manquant sur '{$table}.{$column}'";
            echo "   ⚠️  Index manquant sur '{$table}.{$column}'\n";
        }
    }
}

echo "\n";

// Statistiques
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  Statistiques de la Base de Données                       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$stats = [
    'pays' => 'Pays',
    'region' => 'Régions',
    'ville' => 'Villes',
    'dignitaire' => 'Dignitaires',
    'poste' => 'Postes',
    'nomination' => 'Nominations',
    'decoration' => 'Décorations'
];

foreach ($stats as $table => $label) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        echo sprintf("   %-20s : %d\n", $label, $count);
    }
}

echo "\n";

// Rapport final
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  Rapport Final                                             ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (count($errors) > 0) {
    echo "❌ ERREURS CRITIQUES :\n";
    foreach ($errors as $error) {
        echo "   {$error}\n";
    }
    echo "\n";
    echo "👉 Action requise : Exécutez 'php artisan migrate' pour corriger\n";
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  AVERTISSEMENTS :\n";
    foreach ($warnings as $warning) {
        echo "   {$warning}\n";
    }
    echo "\n";
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo "✅ Tout est en ordre ! Le schéma de la base de données est correct.\n";
    echo "\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";

exit(count($errors) > 0 ? 1 : 0);
