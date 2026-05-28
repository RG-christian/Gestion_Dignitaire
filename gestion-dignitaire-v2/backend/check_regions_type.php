<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 Régions (type='region') :\n\n";

$regions = DB::table('region')->where('type', 'region')->get(['id', 'nom', 'continent']);

foreach ($regions as $r) {
    $continent = $r->continent ?: 'NULL';
    echo "ID: {$r->id} | Nom: {$r->nom} | Continent: {$continent}\n";
}

echo "\nTotal : " . count($regions) . "\n";
