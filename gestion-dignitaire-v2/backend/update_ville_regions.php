<?php

/**
 * Script pour associer les villes aux régions du Gabon
 * À exécuter après la migration add_region_id_to_ville_table
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🇬🇦 Association des villes aux régions du Gabon...\n\n";

// Mapping des villes gabonaises par région
$villesParRegion = [
    'Estuaire' => [
        'Libreville', 'Owendo', 'Akanda', 'Ntoum', 'Kango', 'Cocobeach',
        'Libreville (Gabon)', 'Owendo (Gabon)'
    ],
    'Haut-Ogooué' => [
        'Franceville', 'Moanda', 'Mounana', 'Okondja', 'Bakoumba', 'Lékoni',
        'Franceville (Gabon)'
    ],
    'Moyen-Ogooué' => [
        'Lambaréné', 'Ndjolé', 'Bifoun', 'Lambaréné (Gabon)'
    ],
    'Ngounié' => [
        'Mouila', 'Ndendé', 'Mimongo', 'Lebamba', 'Mbigou', 'Mandji',
        'Mouila (Gabon)'
    ],
    'Nyanga' => [
        'Tchibanga', 'Mayumba', 'Moulengui-Binza', 'Tchibanga (Gabon)'
    ],
    'Ogooué-Ivindo' => [
        'Makokou', 'Mékambo', 'Ovan', 'Booué', 'Makokou (Gabon)'
    ],
    'Ogooué-Lolo' => [
        'Koulamoutou', 'Lastoursville', 'Pana', 'Koulamoutou (Gabon)'
    ],
    'Ogooué-Maritime' => [
        'Port-Gentil', 'Omboué', 'Gamba', 'Port-Gentil (Gabon)'
    ],
    'Woleu-Ntem' => [
        'Oyem', 'Bitam', 'Mitzic', 'Minvoul', 'Oyem (Gabon)'
    ]
];

try {
    DB::beginTransaction();
    
    $totalUpdated = 0;
    $notFound = [];
    
    foreach ($villesParRegion as $nomRegion => $villes) {
        echo "📍 Région: $nomRegion\n";
        
        // Récupérer l'ID de la région
        $region = DB::table('region')->where('nom', $nomRegion)->first();
        
        if (!$region) {
            echo "   ⚠️  Région '$nomRegion' non trouvée dans la base\n";
            continue;
        }
        
        $regionId = $region->id;
        $updated = 0;
        
        foreach ($villes as $nomVille) {
            // Chercher la ville (avec ou sans le suffixe pays)
            $ville = DB::table('ville')
                ->where('nom', 'LIKE', '%' . $nomVille . '%')
                ->orWhere('nom', $nomVille)
                ->first();
            
            if ($ville) {
                DB::table('ville')
                    ->where('id', $ville->id)
                    ->update(['region_id' => $regionId]);
                $updated++;
                $totalUpdated++;
            } else {
                $notFound[] = $nomVille;
            }
        }
        
        echo "   ✅ $updated villes associées\n";
    }
    
    // Pour les villes non trouvées, les associer à Estuaire par défaut
    $estuaire = DB::table('region')->where('nom', 'Estuaire')->first();
    if ($estuaire) {
        $villesSansRegion = DB::table('ville')->whereNull('region_id')->get();
        if ($villesSansRegion->count() > 0) {
            echo "\n📍 Villes sans région (associées à Estuaire par défaut):\n";
            foreach ($villesSansRegion as $ville) {
                DB::table('ville')
                    ->where('id', $ville->id)
                    ->update(['region_id' => $estuaire->id]);
                echo "   - {$ville->nom}\n";
                $totalUpdated++;
            }
        }
    }
    
    DB::commit();
    
    echo "\n✅ Mise à jour terminée !\n";
    echo "📊 Total: $totalUpdated villes associées aux régions\n";
    
    if (count($notFound) > 0) {
        echo "\n⚠️  Villes non trouvées dans la base:\n";
        foreach (array_unique($notFound) as $ville) {
            echo "   - $ville\n";
        }
    }
    
    // Afficher les statistiques
    echo "\n📈 Statistiques par région:\n";
    $stats = DB::table('ville')
        ->join('region', 'ville.region_id', '=', 'region.id')
        ->select('region.nom', DB::raw('COUNT(*) as count'))
        ->groupBy('region.id', 'region.nom')
        ->orderBy('count', 'desc')
        ->get();
    
    foreach ($stats as $stat) {
        echo "   {$stat->nom}: {$stat->count} villes\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Terminé !\n";
