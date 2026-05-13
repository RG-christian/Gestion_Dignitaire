<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Test table diplomes ===\n\n";

// Vérifier la table
$count = DB::table('diplome')->count();
echo "Nombre de diplômes: $count\n\n";

// Afficher quelques diplômes avec jointures
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
    ->take(5)
    ->get();

foreach ($diplomes as $dip) {
    echo "- {$dip->intitule} ({$dip->annee}) - Dignitaire: {$dip->dignitaire_nom}\n";
    echo "  Établissement: {$dip->etablissement_nom}, Domaine: {$dip->domaine_nom}\n\n";
}

echo "=== Test terminé ===\n";
