<?php

/**
 * Script pour nettoyer la province "Estuaire" des pays non-gabonais
 * Estuaire est une province du Gabon uniquement
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧹 Nettoyage de la province 'Estuaire'...\n\n";

// 1. Trouver toutes les régions/provinces nommées "Estuaire"
$estuaireRegions = DB::table('region')
    ->where('nom', 'like', '%Estuaire%')
    ->get();

echo "📊 Régions 'Estuaire' trouvées : " . $estuaireRegions->count() . "\n";

foreach ($estuaireRegions as $region) {
    echo "  - ID: {$region->id}, Nom: {$region->nom}, Type: {$region->type}, Pays: {$region->pays_nom}\n";
}

// 2. Identifier l'Estuaire gabonais (à conserver)
$estuaireGabon = DB::table('region')
    ->where('nom', 'like', '%Estuaire%')
    ->where('pays_nom', 'Gabon')
    ->first();

if ($estuaireGabon) {
    echo "\n✅ Estuaire gabonais trouvé (ID: {$estuaireGabon->id}) - CONSERVÉ\n";
}

// 3. Supprimer les autres "Estuaire" (non-gabonais)
$deleted = DB::table('region')
    ->where('nom', 'like', '%Estuaire%')
    ->where('pays_nom', '!=', 'Gabon')
    ->orWhere(function($query) {
        $query->where('nom', 'like', '%Estuaire%')
              ->whereNull('pays_nom');
    })
    ->delete();

echo "\n🗑️  Régions 'Estuaire' non-gabonaises supprimées : {$deleted}\n";

// 4. Mettre à jour les villes qui pointaient vers ces régions supprimées
$villesAffectees = DB::table('ville')
    ->whereNotNull('region_id')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('region')
              ->whereColumn('region.id', 'ville.region_id');
    })
    ->update(['region_id' => null]);

echo "🏙️  Villes mises à jour (region_id = NULL) : {$villesAffectees}\n";

// 5. Afficher les provinces gabonaises restantes
echo "\n📋 Provinces gabonaises dans la base :\n";
$provincesGabon = DB::table('region')
    ->where('type', 'province')
    ->where('pays_nom', 'Gabon')
    ->orderBy('nom')
    ->get();

foreach ($provincesGabon as $province) {
    echo "  - {$province->nom}\n";
}

echo "\n✅ Nettoyage terminé !\n";
