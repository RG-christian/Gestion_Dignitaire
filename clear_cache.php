<?php
// Vider tous les caches PHP

echo "<h1>Nettoyage du cache PHP</h1>";

// 1. Vider le cache opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p>✅ Cache opcache vidé</p>";
} else {
    echo "<p>⚠️ Opcache non activé</p>";
}

// 2. Vider le cache de réalisation
if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
    echo "<p>✅ Cache APC vidé</p>";
} else {
    echo "<p>⚠️ APC non activé</p>";
}

// 3. Afficher les informations PHP
echo "<h2>Informations PHP</h2>";
echo "<p>Version PHP : " . PHP_VERSION . "</p>";
echo "<p>Opcache activé : " . (function_exists('opcache_get_status') ? 'Oui' : 'Non') . "</p>";

// 4. Vérifier que les fichiers existent
echo "<h2>Vérification des fichiers</h2>";
$files = [
    'config/security.php',
    'config/database.php',
    'config/validator.php',
    'config/logger.php',
    'config/upload.php',
    'config/helpers.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file existe</p>";
    } else {
        echo "<p>❌ $file MANQUANT</p>";
    }
}

// 5. Tester le chargement de security.php
echo "<h2>Test de chargement</h2>";
require_once 'config/security.php';

if (function_exists('secureSession')) {
    echo "<p>✅ Fonction secureSession() chargée avec succès</p>";
} else {
    echo "<p>❌ Fonction secureSession() NON TROUVÉE</p>";
}

echo "<hr>";
echo "<p><strong>Cache vidé ! Essayez maintenant d'accéder à <a href='index.php'>index.php</a></strong></p>";
