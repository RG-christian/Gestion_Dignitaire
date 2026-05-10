<?php
// Script pour vérifier la structure de la base de données existante

$host = 'localhost';
$dbname = 'gestion_dignitaire';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connexion réussie à la base '$dbname'\n\n";
    
    // Lister les tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📊 Tables existantes (" . count($tables) . ") :\n";
    echo str_repeat("=", 50) . "\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    echo "\n";
    
    // Vérifier si la table users existe
    if (in_array('users', $tables)) {
        echo "✓ Table 'users' existe\n";
        
        // Compter les utilisateurs
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        echo "  → $count utilisateur(s) dans la base\n";
    } else {
        echo "⚠ Table 'users' n'existe pas\n";
        echo "  → Il faudra créer cette table\n";
    }
    
    echo "\n";
    
    // Vérifier la table dignitaires
    if (in_array('dignitaires', $tables)) {
        echo "✓ Table 'dignitaires' existe\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM dignitaires");
        $count = $stmt->fetchColumn();
        echo "  → $count dignitaire(s) dans la base\n";
    } else {
        echo "⚠ Table 'dignitaires' n'existe pas\n";
    }
    
    echo "\n";
    echo str_repeat("=", 50) . "\n";
    echo "✓ Vérification terminée\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
