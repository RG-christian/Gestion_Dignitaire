<?php

/**
 * Script pour attribuer les continents aux régions géographiques
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🌍 Attribution des continents aux régions...\n\n";

// Mapping des régions vers leurs continents
$regionsContinents = [
    // Afrique
    'Afrique australe' => 'Afrique',
    'Afrique centrale' => 'Afrique',
    'Afrique de l\'Est' => 'Afrique',
    'Afrique de l\'Ouest' => 'Afrique',
    'Afrique du Nord' => 'Afrique',

    // Amérique
    'Amérique centrale' => 'Amérique',
    'Amérique du Nord' => 'Amérique',
    'Amérique du Sud' => 'Amérique',

    // Asie
    'Asie centrale' => 'Asie',
    'Asie du Sud' => 'Asie',
    'Asie de l\'Est' => 'Asie',
    'Asie du Sud-Est' => 'Asie',
    'Moyen-Orient' => 'Asie',

    // Europe
    'Europe centrale' => 'Europe',
    'Europe de l\'Est' => 'Europe',
    'Europe de l\'Ouest' => 'Europe',
    'Europe du Nord' => 'Europe',
    'Europe du Sud' => 'Europe',

    // Océanie
    'Océanie' => 'Océanie',
    'Australasie' => 'Océanie',
    'Mélanésie' => 'Océanie',
    'Micronésie' => 'Océanie',
    'Polynésie' => 'Océanie'
];

$updated = 0;
$notFound = 0;

// Récupérer toutes les régions (type = 'region')
$regions = DB::table('region')
    ->where('type', 'region')
    ->orWhereNull('type')
    ->get();

echo "📋 Régions trouvées : " . $regions->count() . "\n\n";

foreach ($regions as $region) {
    $continent = null;

    // Chercher le continent correspondant
    foreach ($regionsContinents as $nomRegion => $continentRegion) {
        if (stripos($region->nom, $nomRegion) !== false ||
            stripos($nomRegion, $region->nom) !== false ||
            strtolower($region->nom) === strtolower($nomRegion)) {
            $continent = $continentRegion;
            break;
        }
    }

    if ($continent) {
        DB::table('region')
            ->where('id', $region->id)
            ->update([
                'type' => 'region',
                'continent' => $continent,
                'pays_nom' => null
            ]);
        echo "✅ {$region->nom} → {$continent}\n";
        $updated++;
    } else {
        echo "⚠️  {$region->nom} → Continent non trouvé\n";
        $notFound++;
    }
}

echo "\n📊 Résumé :\n";
echo "  - Régions mises à jour : {$updated}\n";
echo "  - Régions sans continent : {$notFound}\n";

echo "\n✅ Terminé !\n";
