<?php
require_once 'config/database.php';

try {
    $pdo = getDatabaseConnection();
    
    // Récupérer tous les dignitaires
    $dignitaires = $pdo->query("SELECT id, prenom, nom FROM dignitaire")->fetchAll();
    
    foreach ($dignitaires as $dig) {
        $nomEmail = strtolower(str_replace(' ', '.', $dig['prenom'] . '.' . $dig['nom']));
        
        // Ajouter 2 téléphones
        $pdo->exec("
            INSERT INTO dignitaire_telephones (dignitaire_id, numero, type, principal, created_at, updated_at)
            VALUES 
            ({$dig['id']}, '+237 6" . rand(70000000, 79999999) . "', 'mobile', 1, NOW(), NOW()),
            ({$dig['id']}, '+237 2" . rand(20000000, 29999999) . "', 'bureau', 0, NOW(), NOW())
        ");
        
        // Ajouter 2 emails
        $pdo->exec("
            INSERT INTO dignitaire_emails (dignitaire_id, email, type, principal, created_at, updated_at)
            VALUES 
            ({$dig['id']}, '{$nomEmail}@gouv.cm', 'professionnel', 1, NOW(), NOW()),
            ({$dig['id']}, '{$nomEmail}@gmail.com', 'personnel', 0, NOW(), NOW())
        ");
        
        echo "✅ Ajouté téléphones et emails pour {$dig['prenom']} {$dig['nom']}\n";
    }
    
    echo "\n✅ Tous les téléphones et emails ont été ajoutés avec succès!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
