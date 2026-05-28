<?php

/**
 * Script pour ajouter les 9 provinces du Gabon
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🇬🇦 Ajout des provinces gabonaises...\n\n";

$provinces = [
    'Estuaire',
    'Haut-Ogooué',
    'Moyen-Ogooué',
    'Ngounié',
    'Nyanga',
    'Ogooué-Ivindo',
    'Ogooué-Lolo',
    'Ogooué-Maritime',
    'Woleu-Ntem'
];

$added = 0;
$skipped = 0;
$updated = 0;

foreach ($provinces as $province) {
    // Vérifier si la province existe déjà (peu importe le pays)
    $existing = DB::table('region')
        ->where('nom', $province)
        ->first();

    if ($existing) {
        // Vérifier si c'est bien une province gabonaise
        if ($existing->type === 'province' && $existing->pays_nom === 'Gabon') {
            echo "⏭️  {$province} : Déjà existante (Gabon)\n";
            $skipped++;
        } else {
            // Mettre à jour pour en faire une province gabonaise
            DB::table('region')
                ->where('id', $existing->id)
                ->update([
                    'type' => 'province',
                    'pays_nom' => 'Gabon',
                    'continent' => null
                ]);
            echo "🔄 {$province} : Mise à jour vers province gabonaise\n";
            $updated++;
        }
    } else {
        DB::table('region')->insert([
            'nom' => $province,
            'type' => 'province',
            'pays_nom' => 'Gabon',
            'continent' => null
        ]);
        echo "✅ {$province} : Ajoutée\n";
        $added++;
    }
}

echo "\n📊 Résumé :\n";
echo "  - Provinces ajoutées : {$added}\n";
echo "  - Provinces mises à jour : {$updated}\n";
echo "  - Provinces existantes : {$skipped}\n";
echo "  - Total : " . count($provinces) . "\n";

echo "\n✅ Terminé !\n";
