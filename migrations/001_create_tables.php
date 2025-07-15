<?php
require_once __DIR__ . '/../config/database.php';

// Connexion à la base via ta fonction utilitaire
$pdo = getDatabaseConnection();

$sql = "

-- Tables de référence d'abord

CREATE TABLE IF NOT EXISTS pays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL,
    code_iso VARCHAR(3) UNIQUE,
    continent VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS domaine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS code_langue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL,
    code_iso VARCHAR(5),
    famille VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS entite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) UNIQUE NOT NULL,
    type VARCHAR(50),
    description TEXT
);

CREATE TABLE IF NOT EXISTS region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL,
    pays_id INT,
    CONSTRAINT fk_region_pays FOREIGN KEY (pays_id) 
        REFERENCES pays(id) ON DELETE SET NULL
);




CREATE TABLE IF NOT EXISTS ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region_id INT,
    CONSTRAINT fk_ville_region FOREIGN KEY (region_id) 
        REFERENCES region(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS structure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) UNIQUE NOT NULL,
    type VARCHAR(50),
    adresse TEXT,
    ville_id INT,
    CONSTRAINT fk_structure_ville FOREIGN KEY (ville_id) 
        REFERENCES ville(id) ON DELETE SET NULL
);


CREATE TABLE IF NOT EXISTS etablissement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) UNIQUE NOT NULL,
    type VARCHAR(50),
    ville_id INT,
    CONSTRAINT fk_etablissement_ville FOREIGN KEY (ville_id) 
        REFERENCES ville(id) ON DELETE SET NULL
);




-- Table principale
CREATE TABLE IF NOT EXISTS dignitaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) UNIQUE,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    date_naissance DATE,
    lieu_naissance INT,
    genre VARCHAR(10),
    etat_civil VARCHAR(20),
    photo VARCHAR(255),
    CONSTRAINT fk_dignitaire_lieu_naissance FOREIGN KEY (lieu_naissance) 
        REFERENCES ville(id) ON DELETE SET NULL
);
";

try {
    $pdo->exec($sql);
    echo "Migration 001_create_base_tables exécutée avec succès\n";
} catch (PDOException $e) {
    echo "Erreur lors de la migration : " . $e->getMessage() . "\n";
}
?>