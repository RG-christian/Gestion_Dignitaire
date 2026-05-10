<?php
/**
 * Script de test de connexion à la base de données
 * Vérifie que les tables existent et contiennent des données
 */

// Configuration
$host = '127.0.0.1';
$dbname = 'gestion_dignitaire';
$username = 'root';
$password = 'root';

echo "=== TEST CONNEXION BASE DE DONNÉES ===\n\n";

try {
    // Connexion
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connexion à la base de données réussie\n\n";

    // Tables à vérifier
    $tables = [
        'dignitaire' => 'Dignitaires',
        'postes' => 'Postes',
        'decoration' => 'Décorations',
        'ville' => 'Villes',
        'pays' => 'Pays',
        'region' => 'Régions',
        'entites' => 'Entités',
        'users' => 'Utilisateurs'
    ];

    echo "Vérification des tables et comptage des enregistrements:\n";
    echo str_repeat("-", 60) . "\n";

    foreach ($tables as $table => $label) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['count'];
            echo sprintf("%-20s : %5d enregistrements\n", $label, $count);
        } catch (PDOException $e) {
            echo sprintf("%-20s : ✗ Table non trouvée ou erreur\n", $label);
        }
    }

    echo "\n" . str_repeat("-", 60) . "\n";

    // Vérifier la structure de la table dignitaire
    echo "\nStructure de la table 'dignitaire':\n";
    $stmt = $pdo->query("DESCRIBE dignitaire");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})";
        if ($col['Null'] === 'NO') echo " NOT NULL";
        if ($col['Key'] === 'PRI') echo " PRIMARY KEY";
        echo "\n";
    }

    // Vérifier un exemple de dignitaire
    echo "\nExemple de dignitaire (premier enregistrement):\n";
    $stmt = $pdo->query("SELECT * FROM dignitaire LIMIT 1");
    $dignitaire = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($dignitaire) {
        foreach ($dignitaire as $key => $value) {
            $displayValue = $value !== null ? substr($value, 0, 50) : 'NULL';
            echo "  - $key: $displayValue\n";
        }
    } else {
        echo "  Aucun dignitaire trouvé\n";
    }

    // Vérifier la table users
    echo "\nVérification de la table 'users':\n";
    $stmt = $pdo->query("SELECT id, username, nom_complet, role_id FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "  Utilisateurs trouvés:\n";
        foreach ($users as $user) {
            echo "    - {$user['username']} ({$user['nom_complet']}) - Role: {$user['role_id']}\n";
        }
    } else {
        echo "  Aucun utilisateur trouvé\n";
    }

} catch (PDOException $e) {
    echo "✗ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== FIN DES TESTS ===\n";
