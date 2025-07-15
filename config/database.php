<?php
// config/database.php

// Paramètres de connexion
const DB_HOST = 'localhost';
const DB_NAME = 'gestion_dignitaire';
const DB_USER = 'root';
const DB_PASS = 'root';
const DB_PORT = 3307;

// Fonction de connexion PDO
function getDatabaseConnection()
{
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Gestion d'erreurs propre
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Résultats sous forme de tableau associatif
            PDO::ATTR_EMULATE_PREPARES => false                 // Sécurité : vrai mode préparé
        ];

        return new PDO($dsn, DB_USER, DB_PASS, $options);

    } catch (PDOException $e) {
        // Message d’erreur clair, mais à ne pas afficher en production
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }
}
