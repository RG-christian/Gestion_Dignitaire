<?php
$dir = __DIR__ . '/migrations';
$files = scandir($dir);

// Exécute tous les fichiers PHP du dossier migrations dans l’ordre
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        echo "Exécution : $file\n";
        include $dir . '/' . $file;
    }
}
