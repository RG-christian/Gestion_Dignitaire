<?php
/**
 * Script pour créer un utilisateur de test avec mot de passe hashé
 */

// Configuration
$host = '127.0.0.1';
$dbname = 'gestion_dignitaire';
$username = 'root';
$password = 'root';

echo "=== CRÉATION UTILISATEUR DE TEST ===\n\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'utilisateur admin existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo "L'utilisateur 'admin' existe déjà.\n";
        echo "Mise à jour du mot de passe...\n";
        
        // Hasher le mot de passe
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        
        // Mettre à jour
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashedPassword, 'admin']);
        
        echo "✓ Mot de passe mis à jour pour 'admin'\n";
        echo "  Username: admin\n";
        echo "  Password: admin123\n";
    } else {
        echo "Création d'un nouvel utilisateur 'admin'...\n";
        
        // Hasher le mot de passe
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        
        // Insérer
        $stmt = $pdo->prepare("
            INSERT INTO users (username, nom_complet, email, password, role_id) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(['admin', 'Administrateur', 'admin@example.com', $hashedPassword, 1]);
        
        echo "✓ Utilisateur 'admin' créé\n";
        echo "  Username: admin\n";
        echo "  Password: admin123\n";
    }

    // Afficher tous les utilisateurs
    echo "\nListe des utilisateurs:\n";
    $stmt = $pdo->query("SELECT id, username, nom_complet, role_id FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "  - {$user['username']} ({$user['nom_complet']}) - Role: {$user['role_id']}\n";
    }

} catch (PDOException $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== FIN ===\n";
