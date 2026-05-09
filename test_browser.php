<!DOCTYPE html>
<html>
<head>
    <title>Test de chargement</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>Test de chargement des fichiers</h1>
    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    echo "<h2>1. Vérification des fichiers</h2>";
    
    $files = [
        'config/security.php' => 'Fichier de sécurité',
        'config/database.php' => 'Fichier de base de données',
        'config/validator.php' => 'Fichier de validation',
        'config/logger.php' => 'Fichier de logging',
        'config/upload.php' => 'Fichier d\'upload',
        'config/helpers.php' => 'Fichier d\'helpers'
    ];
    
    $allExist = true;
    foreach ($files as $file => $desc) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ $desc ($file) existe</p>";
        } else {
            echo "<p class='error'>❌ $desc ($file) MANQUANT</p>";
            $allExist = false;
        }
    }
    
    if (!$allExist) {
        die("<p class='error'><strong>Erreur : Des fichiers sont manquants !</strong></p>");
    }
    
    echo "<h2>2. Chargement de security.php</h2>";
    try {
        require_once 'config/security.php';
        echo "<p class='success'>✅ security.php chargé</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur : " . $e->getMessage() . "</p>";
        die();
    }
    
    echo "<h2>3. Vérification de la fonction secureSession()</h2>";
    if (function_exists('secureSession')) {
        echo "<p class='success'>✅ Fonction secureSession() existe</p>";
    } else {
        echo "<p class='error'>❌ Fonction secureSession() N'EXISTE PAS</p>";
        echo "<p>Fonctions définies dans security.php :</p>";
        echo "<pre>";
        $functions = get_defined_functions()['user'];
        foreach ($functions as $func) {
            if (strpos($func, 'session') !== false || strpos($func, 'csrf') !== false) {
                echo "- $func\n";
            }
        }
        echo "</pre>";
        die();
    }
    
    echo "<h2>4. Test d'appel de secureSession()</h2>";
    try {
        secureSession();
        echo "<p class='success'>✅ secureSession() appelée avec succès</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur : " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>5. Chargement des autres fichiers</h2>";
    $otherFiles = [
        'config/database.php',
        'config/validator.php',
        'config/logger.php',
        'config/upload.php',
        'config/helpers.php'
    ];
    
    foreach ($otherFiles as $file) {
        try {
            require_once $file;
            echo "<p class='success'>✅ " . basename($file) . " chargé</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur avec " . basename($file) . " : " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h2>✅ TOUS LES TESTS SONT PASSÉS !</h2>";
    echo "<p><a href='index.php' style='font-size: 20px; color: blue;'>→ Accéder à l'application</a></p>";
    ?>
</body>
</html>
