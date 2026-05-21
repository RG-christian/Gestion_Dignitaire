<?php
require_once 'config/database.php';

try {
    $pdo = getDatabaseConnection();
    
    // Table téléphones
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS dignitaire_telephones (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            dignitaire_id BIGINT UNSIGNED NOT NULL,
            numero VARCHAR(20) NOT NULL,
            type VARCHAR(20) DEFAULT 'mobile',
            principal TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX(dignitaire_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    // Table emails
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS dignitaire_emails (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            dignitaire_id BIGINT UNSIGNED NOT NULL,
            email VARCHAR(255) NOT NULL,
            type VARCHAR(20) DEFAULT 'personnel',
            principal TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX(dignitaire_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    echo "✅ Tables dignitaire_telephones et dignitaire_emails créées avec succès\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
