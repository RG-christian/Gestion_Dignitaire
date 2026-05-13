<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== Test Langues Parlées Endpoint ===\n";
$languesParlees = DB::table('langues as lp')
    ->select([
        'lp.*',
        DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
        'l.nom as langue_nom'
    ])
    ->leftJoin('dignitaire as d', 'lp.dignitaire_id', '=', 'd.id')
    ->leftJoin('langue as l', 'lp.langue_id', '=', 'l.id')
    ->take(3)->get();

echo "Count: " . $languesParlees->count() . "\n";
foreach($languesParlees as $lp) {
    echo "- {$lp->dignitaire_nom} parle {$lp->langue_nom} ({$lp->niveau})\n";
}

echo "\n=== Test Postes Endpoint ===\n";
$postes = DB::table('postes as p')
    ->select([
        'p.*',
        DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
        'e.nom as entite_nom',
        'v.nom as ville_nom'
    ])
    ->leftJoin('dignitaire as d', 'p.dignitaire_id', '=', 'd.id')
    ->leftJoin('entite as e', 'p.entite_id', '=', 'e.id')
    ->leftJoin('ville as v', 'p.ville_id', '=', 'v.id')
    ->take(3)->get();

echo "Count: " . $postes->count() . "\n";
foreach($postes as $p) {
    echo "- {$p->intitule} ({$p->dignitaire_nom}) - {$p->entite_nom} - {$p->ville_nom}\n";
}

echo "\n✅ Les requêtes fonctionnent correctement!\n";
