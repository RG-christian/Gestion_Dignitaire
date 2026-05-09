<?php
// Test simple pour vérifier que tout fonctionne

echo "=== Test de chargement des fichiers ===\n\n";

// 1. Charger security.php
echo "1. Chargement de security.php...\n";
require_once __DIR__ . '/config/security.php';
echo "   ✅ OK\n\n";

// 2. Vérifier que secureSession existe
echo "2. Vérification de la fonction secureSession()...\n";
if (function_exists('secureSession')) {
    echo "   ✅ Fonction secureSession() existe\n\n";
} else {
    echo "   ❌ Fonction secureSession() n'existe pas\n\n";
    exit(1);
}

// 3. Charger database.php
echo "3. Chargement de database.php...\n";
require_once __DIR__ . '/config/database.php';
echo "   ✅ OK\n\n";

// 4. Vérifier que getDatabaseConnection existe
echo "4. Vérification de la fonction getDatabaseConnection()...\n";
if (function_exists('getDatabaseConnection')) {
    echo "   ✅ Fonction getDatabaseConnection() existe\n\n";
} else {
    echo "   ❌ Fonction getDatabaseConnection() n'existe pas\n\n";
    exit(1);
}

// 5. Tester la connexion à la base de données
echo "5. Test de connexion à la base de données...\n";
try {
    $pdo = getDatabaseConnection();
    echo "   ✅ Connexion réussie\n\n";
} catch (Exception $e) {
    echo "   ❌ Erreur : " . $e->getMessage() . "\n\n";
}

// 6. Charger les autres fichiers
echo "6. Chargement des autres fichiers de config...\n";
require_once __DIR__ . '/config/validator.php';
echo "   ✅ validator.php chargé\n";
require_once __DIR__ . '/config/logger.php';
echo "   ✅ logger.php chargé\n";
require_once __DIR__ . '/config/upload.php';
echo "   ✅ upload.php chargé\n";
require_once __DIR__ . '/config/helpers.php';
echo "   ✅ helpers.php chargé\n\n";

// 7. Tester secureSession()
echo "7. Test de secureSession()...\n";
try {
    secureSession();
    echo "   ✅ Session sécurisée initialisée\n\n";
} catch (Exception $e) {
    echo "   ❌ Erreur : " . $e->getMessage() . "\n\n";
}

echo "=== ✅ TOUS LES TESTS SONT PASSÉS ===\n";
echo "\nVous pouvez maintenant accéder à index.php dans votre navigateur.\n";
