<?php

/**
 * Script pour ajouter les 9 régions du Gabon
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🇬🇦 Ajout des régions du Gabon...\n\n";

$regionsGabon = [
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

try {
    DB::beginTransaction();

    $added = 0;
    $existing = 0;

    foreach ($regionsGabon as $nomRegion) {
        // Vérifier si la région existe déjà
        $exists = DB::table('region')->where('nom', $nomRegion)->exists();

        if (!$exists) {
            DB::table('region')->insert(['nom' => $nomRegion]);
            echo "✅ Ajouté: $nomRegion\n";
            $added++;
        } else {
            echo "⚠️  Existe déjà: $nomRegion\n";
            $existing++;
        }
    }

    DB::commit();

    echo "\n📊 Résumé:\n";
    echo "   - Régions ajoutées: $added\n";
    echo "   - Régions existantes: $existing\n";
    echo "   - Total: " . count($regionsGabon) . " régions\n";

    echo "\n✅ Terminé !\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
