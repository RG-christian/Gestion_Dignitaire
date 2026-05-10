<?php
// Script pour vérifier la structure de la table users

$host = 'localhost';
$dbname = 'gestion_dignitaire';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connexion réussie\n\n";
    
    // Structure de la table users
    echo "📊 STRUCTURE DE LA TABLE 'users' :\n";
    echo str_repeat("=", 70) . "\n";
    
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        printf("%-20s %-20s %-10s %-10s\n", 
            $col['Field'], 
            $col['Type'], 
            $col['Null'], 
            $col['Key']
        );
    }
    
    echo "\n";
    echo str_repeat("=", 70) . "\n";
    
    // Afficher un exemple d'utilisateur
    echo "\n📋 EXEMPLE D'UTILISATEUR :\n";
    echo str_repeat("=", 70) . "\n";
    
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        foreach ($user as $key => $value) {
            // Masquer le mot de passe
            if ($key === 'password' || $key === 'mot_de_passe') {
                $value = '[MASQUÉ]';
            }
            printf("%-20s : %s\n", $key, $value);
        }
    }
    
    echo "\n";
    echo str_repeat("=", 70) . "\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
