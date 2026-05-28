<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Correction des 2 régions...\n\n";

// ID 2 : Afrique de l'Ouest
DB::table('region')->where('id', 2)->update(['continent' => 'Afrique']);
echo "✅ ID 2 : Afrique de l'Ouest → Afrique\n";

// ID 6 : Europe de l'Ouest
DB::table('region')->where('id', 6)->update(['continent' => 'Europe']);
echo "✅ ID 6 : Europe de l'Ouest → Europe\n";

echo "\n✅ Terminé !\n";
