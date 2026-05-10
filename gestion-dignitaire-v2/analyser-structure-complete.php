<?php
/**
 * Analyse complète de la structure de la base de données
 * pour créer un mapping exact des tables et colonnes
 */

$pdo = new PDO('mysql:host=127.0.0.1;dbname=gestion_dignitaire', 'root', 'root');

echo "=== ANALYSE COMPLÈTE DE LA BASE DE DONNÉES ===\n\n";

// 1. Lister toutes les tables
$stmt = $pdo->query("SHOW TABLES");
$tables = [];
while($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

echo "TABLES TROUVÉES (" . count($tables) . "):\n";
foreach($tables as $table) {
    echo "  - $table\n";
}

// 2. Pour chaque table importante, afficher la structure
$importantTables = [
    'dignitaire', 'postes', 'decoration', 'ville', 'pays', 'region', 
    'entite', 'diplome', 'enfants', 'experiences', 'nominations',
    'langue', 'langues', 'structure', 'etablissement', 'domaine'
];

echo "\n=== STRUCTURE DES TABLES IMPORTANTES ===\n";

foreach($importantTables as $table) {
    if (!in_array($table, $tables)) {
        echo "\n⚠️  Table '$table' n'existe pas\n";
        continue;
    }
    
    echo "\n--- Table: $table ---\n";
    $stmt = $pdo->query("DESCRIBE $table");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($columns as $col) {
        $nullable = $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $key = $col['Key'] ? " [{$col['Key']}]" : '';
        echo "  {$col['Field']}: {$col['Type']} $nullable$key\n";
    }
    
    // Compter les enregistrements
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "  → $count enregistrements\n";
}

// 3. Vérifier les clés étrangères
echo "\n=== RELATIONS (Foreign Keys) ===\n";
$stmt = $pdo->query("
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'gestion_dignitaire'
    AND REFERENCED_TABLE_NAME IS NOT NULL
    ORDER BY TABLE_NAME
");

$relations = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($relations as $rel) {
    echo "  {$rel['TABLE_NAME']}.{$rel['COLUMN_NAME']} → {$rel['REFERENCED_TABLE_NAME']}.{$rel['REFERENCED_COLUMN_NAME']}\n";
}

echo "\n=== FIN DE L'ANALYSE ===\n";
