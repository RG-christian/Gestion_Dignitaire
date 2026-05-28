<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 Liste des pays enregistrés :\n\n";

$pays = DB::table('pays')->orderBy('nom')->get(['id', 'nom', 'code_iso', 'continent']);

echo "Total : " . $pays->count() . " pays\n\n";

foreach($pays as $p) {
    echo sprintf("%-3d | %-30s | %-4s | %s\n", $p->id, $p->nom, $p->code_iso, $p->continent ?? 'N/A');
}
