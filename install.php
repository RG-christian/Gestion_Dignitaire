#!/usr/bin/env php
<?php
/**
 * Script d'installation de l'application Gestion Dignitaire
 * Usage: php install.php
 */

echo "=== Installation de Gestion Dignitaire ===\n\n";

// 1. Vérifier la version PHP
echo "1. Vérification de la version PHP...\n";
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die("❌ PHP 7.4 ou supérieur est requis. Version actuelle: " . PHP_VERSION . "\n");
}
echo "✅ PHP " . PHP_VERSION . " détecté\n\n";

// 2. Vérifier les extensions requises
echo "2. Vérification des extensions PHP...\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'fileinfo', 'mbstring'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
        echo "❌ Extension manquante: $ext\n";
    } else {
        echo "✅ Extension $ext présente\n";
    }
}

if (!empty($missingExtensions)) {
    die("\n❌ Veuillez installer les extensions manquantes: " . implode(', ', $missingExtensions) . "\n");
}
echo "\n";

// 3. Créer le fichier .env s'il n'existe pas
echo "3. Configuration de l'environnement...\n";
if (!file_exists('.env')) {
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ Fichier .env créé depuis .env.example\n";
        echo "⚠️  IMPORTANT: Éditez le fichier .env avec vos paramètres de base de données\n\n";
    } else {
        echo "❌ Fichier .env.example introuvable\n";
    }
} else {
    echo "✅ Fichier .env déjà présent\n\n";
}

// 4. Créer les dossiers nécessaires
echo "4. Création des dossiers...\n";
$directories = [
    'logs' => 0755,
    'uploads' => 0755,
    'uploads/photos' => 0755,
];

foreach ($directories as $dir => $permissions) {
    if (!is_dir($dir)) {
        mkdir($dir, $permissions, true);
        echo "✅ Dossier créé: $dir\n";
    } else {
        echo "✅ Dossier existant: $dir\n";
    }
    
    // Vérifier les permissions
    if (is_writable($dir)) {
        echo "   ✅ Permissions OK\n";
    } else {
        echo "   ⚠️  Permissions insuffisantes, exécutez: chmod $permissions $dir\n";
    }
}
echo "\n";

// 5. Créer un fichier .htaccess pour uploads
echo "5. Sécurisation du dossier uploads...\n";
$htaccessContent = <<<HTACCESS
# Interdire l'exécution de scripts
<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|sh|cgi)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Autoriser uniquement certains types de fichiers
<FilesMatch "\.(jpg|jpeg|png|gif|webp|pdf|doc|docx)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
HTACCESS;

file_put_contents('uploads/.htaccess', $htaccessContent);
echo "✅ Fichier .htaccess créé dans uploads/\n\n";

// 6. Tester la connexion à la base de données
echo "6. Test de connexion à la base de données...\n";
if (file_exists('.env')) {
    require_once 'config/database.php';
    
    try {
        $pdo = getDatabaseConnection();
        echo "✅ Connexion à la base de données réussie\n\n";
        
        // 7. Exécuter les migrations
        echo "7. Exécution des migrations...\n";
        $response = readline("Voulez-vous exécuter les migrations maintenant? (o/n): ");
        
        if (strtolower($response) === 'o') {
            $migrations = glob('migrations/*.php');
            sort($migrations);
            
            foreach ($migrations as $migration) {
                echo "   Exécution de " . basename($migration) . "...\n";
                include $migration;
            }
            echo "✅ Migrations terminées\n\n";
        } else {
            echo "⚠️  Migrations ignorées. Exécutez-les manuellement avec: php run_migrations.php\n\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
        echo "⚠️  Vérifiez vos paramètres dans le fichier .env\n\n";
    }
} else {
    echo "⚠️  Fichier .env non trouvé, test de connexion ignoré\n\n";
}

// 8. Générer une clé secrète
echo "8. Génération d'une clé secrète...\n";
$secretKey = bin2hex(random_bytes(32));
echo "✅ Clé générée: $secretKey\n";
echo "⚠️  Ajoutez cette clé dans votre fichier .env:\n";
echo "   APP_SECRET_KEY=$secretKey\n\n";

// 9. Résumé
echo "=== Installation terminée ===\n\n";
echo "Prochaines étapes:\n";
echo "1. Éditez le fichier .env avec vos paramètres\n";
echo "2. Créez la base de données MySQL si ce n'est pas déjà fait\n";
echo "3. Exécutez les migrations: php run_migrations.php\n";
echo "4. Accédez à l'application via votre navigateur\n";
echo "5. Consultez README.md pour plus d'informations\n\n";

echo "Pour la sécurité:\n";
echo "- Lisez SECURITY_GUIDE.md\n";
echo "- Changez APP_ENV=production en production\n";
echo "- Utilisez HTTPS en production\n";
echo "- Configurez des sauvegardes régulières\n\n";

echo "✅ Installation réussie!\n";
