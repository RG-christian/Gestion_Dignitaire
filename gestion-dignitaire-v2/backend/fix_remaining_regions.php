<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Mise à jour des régions manquantes...\n\n";

$updates = [
    "Afrique de l'Ouest" => 'Afrique',
    "Afrique de l'Est" => 'Afrique',
    "Europe de l'Ouest" => 'Europe',
    "Asie orientale" => 'Asie',
    "Caraïbes" => 'Amérique'
];

foreach ($updates as $nom => $continent) {
    $updated = DB::table('region')
        ->where('nom', $nom)
        ->update([
            'continent' => $continent,
            'type' => 'region',
            'pays_nom' => null
        ]);

    if ($updated) {
        echo "✅ {$nom} → {$continent}\n";
    } else {
        echo "⚠️  {$nom} → Non trouvée\n";
    }
}

echo "\n✅ Terminé !\n";
