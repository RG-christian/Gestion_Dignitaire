<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Enfant;
use Illuminate\Support\Facades\DB;

echo "=== Test de la table enfants ===\n\n";

// Test 1: Compter les enfants
$count = Enfant::count();
echo "Nombre d'enfants dans la base: $count\n\n";

// Test 2: Afficher quelques enfants
echo "Premiers enfants:\n";
$enfants = Enfant::take(5)->get();
foreach ($enfants as $enfant) {
    echo "- ID: {$enfant->id}, Nom: {$enfant->nom} {$enfant->prenom}, Dignitaire ID: {$enfant->dignitaire_id}\n";
}

echo "\n";

// Test 3: Test de la requête optimisée
echo "Test de la requête optimisée:\n";
$enfantsOptimises = DB::table('enfants')
    ->select([
        'enfants.*',
        DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom_complet"),
        'v.nom as lieu_naissance_nom'
    ])
    ->leftJoin('dignitaire as d', 'enfants.dignitaire_id', '=', 'd.id')
    ->leftJoin('ville as v', 'enfants.lieu_naissance', '=', 'v.id')
    ->take(5)
    ->get();

foreach ($enfantsOptimises as $enfant) {
    echo "- {$enfant->nom} {$enfant->prenom} - Dignitaire: {$enfant->dignitaire_nom_complet} - Lieu: {$enfant->lieu_naissance_nom}\n";
}

echo "\n=== Test terminé ===\n";
