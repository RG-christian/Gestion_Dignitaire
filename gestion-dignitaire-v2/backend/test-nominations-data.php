<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== Nominations Data ===\n";
$nominations = DB::table('nominations')->take(5)->get();
foreach($nominations as $n) {
    echo "ID: {$n->id} - Fonction: " . ($n->fonction ?? 'NULL') . " - Poste_id: " . ($n->poste_id ?? 'NULL') . "\n";
}

echo "\n=== Nominations avec JOIN ===\n";
$nominations = DB::table('nominations as n')
    ->select([
        'n.*',
        DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
        'e.nom as entite_nom',
        'p.intitule as poste_nom'
    ])
    ->leftJoin('dignitaire as d', 'n.dignitaire_id', '=', 'd.id')
    ->leftJoin('entite as e', 'n.entite_id', '=', 'e.id')
    ->leftJoin('postes as p', 'n.poste_id', '=', 'p.id')
    ->take(5)->get();

foreach($nominations as $n) {
    echo "Dignitaire: {$n->dignitaire_nom} - Entité: {$n->entite_nom} - Poste: " . ($n->poste_nom ?? 'NULL') . " - Fonction: " . ($n->fonction ?? 'NULL') . "\n";
}
