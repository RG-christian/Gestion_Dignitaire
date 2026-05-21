<?php
require_once 'config/database.php';

try {
    $pdo = getDatabaseConnection();
    
    // Vérifier les tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables trouvées : " . count($tables) . "\n\n";
    
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "$table : $count lignes\n";
    }
    
    // Vérifier spécifiquement la table dignitaire
    if (in_array('dignitaire', $tables)) {
        echo "\n=== Premiers dignitaires ===\n";
        $dignitaires = $pdo->query("SELECT id, matricule, nom, prenom FROM dignitaire LIMIT 5")->fetchAll();
        foreach ($dignitaires as $d) {
            echo "ID {$d['id']}: {$d['prenom']} {$d['nom']} ({$d['matricule']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}
