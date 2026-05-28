<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Ajout de la foreign key region_id...\n";

try {
    // Modifier le type de la colonne pour correspondre à region.id
    DB::statement('ALTER TABLE ville MODIFY COLUMN region_id INT(11) NULL');
    echo "✅ Type de colonne modifié\n";

    // Ajouter la foreign key
    DB::statement('ALTER TABLE ville ADD CONSTRAINT ville_region_id_foreign FOREIGN KEY (region_id) REFERENCES region(id) ON DELETE SET NULL');
    echo "✅ Foreign key ajoutée\n";

    // Ajouter l'index
    DB::statement('ALTER TABLE ville ADD INDEX ville_region_id_index (region_id)');
    echo "✅ Index ajouté\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Terminé !\n";
