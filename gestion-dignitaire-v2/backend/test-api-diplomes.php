<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Test API Diplomes ===\n\n";

// Test direct de la requête
$diplomes = DB::table('diplome as d')
    ->select([
        'd.*',
        DB::raw("CONCAT(dig.prenom, ' ', dig.nom) as dignitaire_nom"),
        'etab.nom as etablissement_nom',
        'dom.nom as domaine_nom',
        'v.nom as ville_nom'
    ])
    ->leftJoin('dignitaire as dig', 'd.dignitaire_id', '=', 'dig.id')
    ->leftJoin('etablissement as etab', 'd.etablissement_id', '=', 'etab.id')
    ->leftJoin('domaine as dom', 'd.domaine_id', '=', 'dom.id')
    ->leftJoin('ville as v', 'd.ville_id', '=', 'v.id')
    ->orderBy('d.annee', 'desc')
    ->get();

echo "Nombre de diplômes: " . $diplomes->count() . "\n\n";

foreach ($diplomes as $dip) {
    echo json_encode($dip, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
}

echo "=== Test terminé ===\n";
