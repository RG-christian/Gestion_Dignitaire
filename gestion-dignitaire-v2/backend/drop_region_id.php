<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Suppression de la colonne region_id...\n";

try {
    DB::statement('ALTER TABLE ville DROP FOREIGN KEY ville_region_id_foreign');
    echo "✅ Foreign key supprimée\n";
} catch (Exception $e) {
    echo "⚠️  Foreign key: " . $e->getMessage() . "\n";
}

try {
    DB::statement('ALTER TABLE ville DROP INDEX ville_region_id_index');
    echo "✅ Index supprimé\n";
} catch (Exception $e) {
    echo "⚠️  Index: " . $e->getMessage() . "\n";
}

try {
    DB::statement('ALTER TABLE ville DROP COLUMN region_id');
    echo "✅ Colonne region_id supprimée\n";
} catch (Exception $e) {
    echo "⚠️  Colonne: " . $e->getMessage() . "\n";
}

echo "\n✅ Terminé !\n";
