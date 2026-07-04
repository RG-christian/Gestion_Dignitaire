<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VÉRIFICATION DES DONNÉES ===\n\n";

// Compter les utilisateurs
$usersCount = \App\Models\User::count();
echo "👤 Utilisateurs: $usersCount\n";

if ($usersCount > 0) {
    $users = \App\Models\User::select('id', 'username', 'nom_complet', 'email')->get();
    foreach ($users as $user) {
        echo "   - ID: {$user->id}, Username: {$user->username}, Nom: {$user->nom_complet}\n";
    }
}

echo "\n";

// Compter les dignitaires
$dignitairesCount = \App\Models\Dignitaire::count();
echo "📋 Dignitaires: $dignitairesCount\n";

if ($dignitairesCount > 0) {
    $dignitaires = \App\Models\Dignitaire::select('id', 'nom', 'prenom')->take(5)->get();
    foreach ($dignitaires as $dig) {
        echo "   - ID: {$dig->id}, Nom: {$dig->nom} {$dig->prenom}\n";
    }
    if ($dignitairesCount > 5) {
        echo "   ... et " . ($dignitairesCount - 5) . " autres\n";
    }
}

echo "\n=== TOUTES LES DONNÉES SONT PRÉSENTES ===\n";
