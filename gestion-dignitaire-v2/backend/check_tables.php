<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 Tables dans la base de données :\n\n";

$tables = DB::select('SHOW TABLES');
$dbName = DB::getDatabaseName();

foreach ($tables as $table) {
    $tableName = $table->{"Tables_in_{$dbName}"};
    echo "  - {$tableName}\n";
}
