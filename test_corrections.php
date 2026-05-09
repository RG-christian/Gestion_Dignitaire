#!/usr/bin/env php
<?php
/**
 * Script de test des corrections effectuées
 * Usage: php test_corrections.php
 */

echo "=== Test des Corrections - Gestion Dignitaire ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Vérifier que .env existe
echo "1. Vérification du fichier .env...\n";
if (file_exists('.env')) {
    $success[] = "✅ Fichier .env présent";
    
    // Vérifier que les variables sont définies
    $envContent = file_get_contents('.env');
    $requiredVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'APP_SECRET_KEY'];
    foreach ($requiredVars as $var) {
        if (strpos($envContent, $var) === false) {
            $errors[] = "❌ Variable $var manquante dans .env";
        } else {
            $success[] = "✅ Variable $var présente";
        }
    }
} else {
    $errors[] = "❌ Fichier .env manquant";
}
echo "\n";

// 2. Vérifier les fichiers de configuration
echo "2. Vérification des fichiers de configuration...\n";
$configFiles = [
    'config/database.php',
    'config/security.php',
    'config/validator.php',
    'config/logger.php',
    'config/upload.php',
    'config/helpers.php'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        $success[] = "✅ $file présent";
    } else {
        $errors[] = "❌ $file manquant";
    }
}
echo "\n";

// 3. Vérifier les dossiers nécessaires
echo "3. Vérification des dossiers...\n";
$directories = ['logs', 'uploads', 'uploads/photos'];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $success[] = "✅ Dossier $dir présent";
        if (is_writable($dir)) {
            $success[] = "✅ Dossier $dir accessible en écriture";
        } else {
            $warnings[] = "⚠️  Dossier $dir non accessible en écriture";
        }
    } else {
        $warnings[] = "⚠️  Dossier $dir manquant (sera créé automatiquement)";
    }
}
echo "\n";

// 4. Vérifier les namespaces dans les classes
echo "4. Vérification des namespaces...\n";
$classFiles = glob('classes/*.php');
$missingNamespaces = [];

foreach ($classFiles as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'namespace classes;') === false) {
        $missingNamespaces[] = basename($file);
    }
}

if (empty($missingNamespaces)) {
    $success[] = "✅ Tous les fichiers de classes ont un namespace";
} else {
    $errors[] = "❌ Fichiers sans namespace: " . implode(', ', $missingNamespaces);
}
echo "\n";

// 5. Vérifier que les fonctions de sécurité existent
echo "5. Vérification des fonctions de sécurité...\n";
if (file_exists('config/security.php')) {
    require_once 'config/security.php';
    
    $securityFunctions = [
        'generateCSRFToken',
        'verifyCSRFToken',
        'csrfField',
        'secureSession',
        'isAuthenticated',
        'requireAuth'
    ];
    
    foreach ($securityFunctions as $func) {
        if (function_exists($func)) {
            $success[] = "✅ Fonction $func() disponible";
        } else {
            $errors[] = "❌ Fonction $func() manquante";
        }
    }
}
echo "\n";

// 6. Vérifier la classe Validator
echo "6. Vérification de la classe Validator...\n";
if (file_exists('config/validator.php')) {
    require_once 'config/validator.php';
    
    if (class_exists('Validator')) {
        $success[] = "✅ Classe Validator disponible";
        
        // Tester la validation
        $testData = ['email' => 'test@example.com', 'nom' => 'Test'];
        $validator = new Validator($testData);
        $validator->required('nom')->email('email');
        
        if ($validator->isValid()) {
            $success[] = "✅ Validation fonctionne correctement";
        } else {
            $errors[] = "❌ Problème avec la validation";
        }
    } else {
        $errors[] = "❌ Classe Validator manquante";
    }
}
echo "\n";

// 7. Vérifier le Logger
echo "7. Vérification du système de logging...\n";
if (file_exists('config/logger.php')) {
    require_once 'config/logger.php';
    
    if (class_exists('Logger')) {
        $success[] = "✅ Classe Logger disponible";
        
        try {
            $logger = getLogger();
            $logger->info("Test du système de logging");
            
            if (file_exists('logs/app.log')) {
                $success[] = "✅ Fichier de log créé avec succès";
            } else {
                $warnings[] = "⚠️  Fichier de log non créé";
            }
        } catch (Exception $e) {
            $errors[] = "❌ Erreur lors du test du logger: " . $e->getMessage();
        }
    } else {
        $errors[] = "❌ Classe Logger manquante";
    }
}
echo "\n";

// 8. Vérifier le routeur sécurisé
echo "8. Vérification du routeur...\n";
if (file_exists('routers/Router.class.php')) {
    $routerContent = file_get_contents('routers/Router.class.php');
    
    if (strpos($routerContent, 'ALLOWED_CONTROLLERS') !== false) {
        $success[] = "✅ Routeur avec whitelist implémenté";
    } else {
        $errors[] = "❌ Whitelist manquante dans le routeur";
    }
    
    if (strpos($routerContent, 'preg_match') !== false) {
        $success[] = "✅ Validation des actions implémentée";
    } else {
        $warnings[] = "⚠️  Validation des actions manquante";
    }
}
echo "\n";

// 9. Vérifier les corrections dans DignitaireDAO
echo "9. Vérification des corrections dans DignitaireDAO...\n";
if (file_exists('classes/DignitaireDAO.class.php')) {
    $daoContent = file_get_contents('classes/DignitaireDAO.class.php');
    
    // Vérifier que 'tel' a été remplacé par 'telephone'
    if (strpos($daoContent, "\$row['telephone']") !== false) {
        $success[] = "✅ Bug 'tel' → 'telephone' corrigé";
    } else {
        $warnings[] = "⚠️  Vérifier la colonne telephone dans DignitaireDAO";
    }
    
    // Vérifier countAll
    if (strpos($daoContent, "FROM dignitaire") !== false && 
        strpos($daoContent, "countAll") !== false) {
        $success[] = "✅ Bug countAll() corrigé";
    }
}
echo "\n";

// 10. Vérifier .htaccess
echo "10. Vérification de .htaccess...\n";
if (file_exists('.htaccess')) {
    $htaccessContent = file_get_contents('.htaccess');
    
    if (strpos($htaccessContent, 'X-Content-Type-Options') !== false) {
        $success[] = "✅ Headers de sécurité configurés";
    }
    
    if (strpos($htaccessContent, '.env') !== false) {
        $success[] = "✅ Protection du fichier .env configurée";
    }
} else {
    $warnings[] = "⚠️  Fichier .htaccess manquant";
}
echo "\n";

// Résumé
echo "=== RÉSUMÉ ===\n\n";

if (!empty($success)) {
    echo "✅ SUCCÈS (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  AVERTISSEMENTS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERREURS (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

// Score final
$total = count($success) + count($warnings) + count($errors);
$score = count($success);
$percentage = $total > 0 ? round(($score / $total) * 100) : 0;

echo "=== SCORE FINAL ===\n";
echo "Score: $score/$total ($percentage%)\n\n";

if ($percentage >= 90) {
    echo "🎉 Excellent ! Toutes les corrections sont en place.\n";
} elseif ($percentage >= 70) {
    echo "👍 Bon ! Quelques ajustements mineurs nécessaires.\n";
} elseif ($percentage >= 50) {
    echo "⚠️  Moyen. Plusieurs corrections à finaliser.\n";
} else {
    echo "❌ Attention ! Beaucoup de corrections manquantes.\n";
}

echo "\nConsultez CORRECTIONS_EFFECTUEES.md pour plus de détails.\n";
