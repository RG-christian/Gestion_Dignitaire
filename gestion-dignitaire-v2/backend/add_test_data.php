<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Ajouter des téléphones et emails pour les dignitaires existants
$dignitaires = DB::table('dignitaire')->limit(5)->get();

foreach ($dignitaires as $dignitaire) {
    echo "Ajout de données pour {$dignitaire->prenom} {$dignitaire->nom}...\n";
    
    // Ajouter 2 téléphones
    DB::table('dignitaire_telephones')->insert([
        [
            'dignitaire_id' => $dignitaire->id,
            'numero' => '+237 6' . rand(70000000, 79999999),
            'type' => 'mobile',
            'principal' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'dignitaire_id' => $dignitaire->id,
            'numero' => '+237 6' . rand(90000000, 99999999),
            'type' => 'bureau',
            'principal' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);
    
    // Ajouter 2 emails
    $nomEmail = strtolower(str_replace(' ', '.', $dignitaire->prenom . '.' . $dignitaire->nom));
    DB::table('dignitaire_emails')->insert([
        [
            'dignitaire_id' => $dignitaire->id,
            'email' => $nomEmail . '@gouv.cm',
            'type' => 'professionnel',
            'principal' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'dignitaire_id' => $dignitaire->id,
            'email' => $nomEmail . '@gmail.com',
            'type' => 'personnel',
            'principal' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);
}

echo "\nDonnées de test ajoutées avec succès!\n";
