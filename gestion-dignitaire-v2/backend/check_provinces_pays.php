<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 Provinces sans pays_nom :\n\n";

$provinces = DB::table('region')
    ->where('type', 'province')
    ->whereNull('pays_nom')
    ->orWhere('pays_nom', '')
    ->get(['id', 'nom', 'pays_nom']);

if (count($provinces) === 0) {
    echo "✅ Toutes les provinces ont un pays_nom !\n";
} else {
    foreach ($provinces as $p) {
        echo "ID: {$p->id} | Nom: {$p->nom} | Pays: NULL\n";
    }
    echo "\nTotal : " . count($provinces) . "\n";
}
