<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 Régions sans continent :\n\n";

$regions = DB::table('region')
    ->whereNull('continent')
    ->orWhere('continent', '')
    ->orWhere('continent', 'N/A')
    ->get();

foreach($regions as $r) {
    echo sprintf("ID: %-3d | Type: %-10s | Nom: %s\n", $r->id, $r->type ?? 'NULL', $r->nom);
}

echo "\nTotal : " . $regions->count() . "\n";
