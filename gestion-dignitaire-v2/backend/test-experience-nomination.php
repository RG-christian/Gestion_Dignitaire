<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "=== Table experience ===\n";
echo "Count: " . DB::table('experience')->count() . "\n";
$experiences = DB::table('experience')->take(1)->get();
if ($experiences->count() > 0) {
    echo "Columns: " . implode(', ', array_keys((array)$experiences->first())) . "\n";
}

echo "\n=== Test Experience Query ===\n";
$experiences = DB::table('experience as e')
    ->select([
        'e.*',
        DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
        's.nom as structure_nom'
    ])
    ->leftJoin('dignitaire as d', 'e.dignitaire_id', '=', 'd.id')
    ->leftJoin('structure as s', 'e.structure_id', '=', 's.id')
    ->take(3)->get();

echo "Count: " . $experiences->count() . "\n";
foreach($experiences as $exp) {
    echo "- {$exp->intitule} ({$exp->dignitaire_nom}) - {$exp->structure_nom}\n";
}

echo "\n=== Table nomination ===\n";
echo "Count: " . DB::table('nomination')->count() . "\n";
$nominations = DB::table('nomination')->take(1)->get();
if ($nominations->count() > 0) {
    echo "Columns: " . implode(', ', array_keys((array)$nominations->first())) . "\n";
}

echo "\n=== Test Nomination Query ===\n";
$nominations = DB::table('nomination as n')
    ->select([
        'n.*',
        DB::raw("CONCAT(d.prenom, ' ', d.nom) as dignitaire_nom"),
        'e.nom as entite_nom'
    ])
    ->leftJoin('dignitaire as d', 'n.dignitaire_id', '=', 'd.id')
    ->leftJoin('entite as e', 'n.entite_id', '=', 'e.id')
    ->take(3)->get();

echo "Count: " . $nominations->count() . "\n";
foreach($nominations as $nom) {
    echo "- {$nom->dignitaire_nom} - {$nom->entite_nom}\n";
}
