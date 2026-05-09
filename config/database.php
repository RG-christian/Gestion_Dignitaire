<?php
// config/database.php

// Chargement des variables d'environnement
function loadEnv($path = __DIR__ . '/../.env')
{
    if (!file_exists($path)) {
        // Si .env n'existe pas, utiliser les valeurs par défaut
        // Cela permet au projet de fonctionner même sans .env
        if (!isset($_ENV['DB_HOST'])) {
            $_ENV['DB_HOST'] = 'localhost';
            $_ENV['DB_PORT'] = '3306';
            $_ENV['DB_NAME'] = 'gestion_dignitaire';
            $_ENV['DB_USER'] = 'root';
            $_ENV['DB_PASS'] = 'root';
            $_ENV['APP_ENV'] = 'development';
            $_ENV['APP_SECRET_KEY'] = 'default_key_change_me_in_production';
        }
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        if (strpos($line, '=') === false) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Charger les variables d'environnement
loadEnv();

// Fonction de connexion PDO
function getDatabaseConnection()
{
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_NAME']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);

    } catch (PDOException $e) {
        // En production, logger l'erreur sans l'afficher
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
            error_log('Database connection error: ' . $e->getMessage());
            die('Erreur de connexion à la base de données. Contactez l\'administrateur.');
        }
        die('Erreur de connexion : ' . $e->getMessage());
    }
}
