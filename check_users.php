<?php
require_once 'config/database.php';

try {
    $pdo = getDatabaseConnection();
    
    echo "=== Utilisateurs dans la base ===\n\n";
    $users = $pdo->query("SELECT id, username, nom_complet, email FROM users")->fetchAll();
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Username: {$user['username']}\n";
        echo "Nom: {$user['nom_complet']}\n";
        echo "Email: {$user['email']}\n";
        echo "---\n";
    }
    
    // Vérifier si admin1 existe
    $admin = $pdo->query("SELECT * FROM users WHERE username = 'admin1'")->fetch();
    if ($admin) {
        echo "\n✅ L'utilisateur admin1 existe\n";
        echo "Mot de passe hashé: " . substr($admin['password'], 0, 20) . "...\n";
    } else {
        echo "\n❌ L'utilisateur admin1 n'existe pas\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
