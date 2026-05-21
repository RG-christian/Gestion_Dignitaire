<?php
require 'gestion-dignitaire-v2/backend/vendor/autoload.php';

$app = require_once 'gestion-dignitaire-v2/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Decoration;

try {
    $decorations = Decoration::all();
    
    echo "JSON retourné par l'API :" . PHP_EOL;
    echo json_encode($decorations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . PHP_EOL;
}
