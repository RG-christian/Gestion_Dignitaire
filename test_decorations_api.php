<?php
require 'gestion-dignitaire-v2/backend/vendor/autoload.php';

$app = require_once 'gestion-dignitaire-v2/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Decoration;

try {
    $decorations = Decoration::all();
    
    echo "Nombre de décorations : " . $decorations->count() . PHP_EOL;
    echo PHP_EOL;
    
    foreach ($decorations as $deco) {
        echo "ID: {$deco->deco_id}" . PHP_EOL;
        echo "Nom: {$deco->nom}" . PHP_EOL;
        echo "Type: {$deco->type}" . PHP_EOL;
        echo "Niveau: {$deco->niveau}" . PHP_EOL;
        echo "---" . PHP_EOL;
    }
    
    echo PHP_EOL . "✅ Test réussi !" . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . PHP_EOL;
}
