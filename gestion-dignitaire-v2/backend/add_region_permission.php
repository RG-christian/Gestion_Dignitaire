<?php

/**
 * Script pour ajouter la sous-fonction "Région" à la fonction "Géographie"
 * et l'attribuer à tous les utilisateurs qui ont accès à "Géographie"
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Ajout de la sous-fonction 'Région' à Géographie...\n\n";

// 1. Trouver la fonction "Géographie"
$fonctionGeo = DB::table('fonctions')
    ->where('fonction_name', 'Géographie')
    ->orWhere('fonction_name', 'LIKE', '%Géographie%')
    ->orWhere('fonction_name', 'LIKE', '%géographie%')
    ->first();

if (!$fonctionGeo) {
    echo "❌ Fonction 'Géographie' non trouvée\n";
    echo "Fonctions disponibles :\n";
    $fonctions = DB::table('fonctions')->get();
    foreach ($fonctions as $f) {
        echo "  - {$f->fonction_name} (ID: {$f->id})\n";
    }
    exit(1);
}

echo "✅ Fonction trouvée : {$fonctionGeo->fonction_name} (ID: {$fonctionGeo->id})\n\n";

// 2. Vérifier si la sous-fonction "Région" existe
$sousfonctionRegion = DB::table('sousfonctions')
    ->where('fonction_id', $fonctionGeo->id)
    ->where(function($query) {
        $query->where('sousfonction_name', 'Région')
              ->orWhere('sousfonction_name', 'Region')
              ->orWhere('sousfonction_name', 'Régions')
              ->orWhere('sousfonction_name', 'Regions');
    })
    ->first();

if ($sousfonctionRegion) {
    echo "✅ Sous-fonction 'Région' existe déjà (ID: {$sousfonctionRegion->id})\n";
} else {
    // Créer la sous-fonction
    $sousfonctionId = DB::table('sousfonctions')->insertGetId([
        'fonction_id' => $fonctionGeo->id,
        'sousfonction_name' => 'Région'
    ]);
    echo "✅ Sous-fonction 'Région' créée (ID: {$sousfonctionId})\n";
    $sousfonctionRegion = DB::table('sousfonctions')->where('id', $sousfonctionId)->first();
}

echo "\n📋 Sous-fonctions de Géographie :\n";
$sousfonctions = DB::table('sousfonctions')
    ->where('fonction_id', $fonctionGeo->id)
    ->get();
foreach ($sousfonctions as $sf) {
    echo "  - {$sf->sousfonction_name} (ID: {$sf->id})\n";
}

// 3. Trouver tous les utilisateurs qui ont accès à la fonction "Géographie"
echo "\n👥 Utilisateurs avec accès à Géographie :\n";
$users = DB::table('user_fonctions')
    ->join('users', 'user_fonctions.user_id', '=', 'users.id')
    ->where('user_fonctions.fonction_id', $fonctionGeo->id)
    ->select('users.id', 'users.nom_complet', 'users.email')
    ->distinct()
    ->get();

if ($users->isEmpty()) {
    echo "  ⚠️  Aucun utilisateur trouvé avec accès à Géographie\n";
} else {
    foreach ($users as $user) {
        echo "  - {$user->nom_complet} ({$user->email})\n";

        // Vérifier si l'utilisateur a déjà accès à la sous-fonction Région
        $hasAccess = DB::table('user_sousfonctions')
            ->where('user_id', $user->id)
            ->where('sousfonction_id', $sousfonctionRegion->id)
            ->exists();

        if ($hasAccess) {
            echo "    ✅ A déjà accès à Région\n";
        } else {
            // Ajouter l'accès
            DB::table('user_sousfonctions')->insert([
                'user_id' => $user->id,
                'sousfonction_id' => $sousfonctionRegion->id
            ]);
            echo "    ✅ Accès à Région ajouté\n";
        }
    }
}

echo "\n✅ Terminé !\n";
echo "\n💡 Déconnectez-vous et reconnectez-vous pour voir 'Région' dans le menu.\n";
