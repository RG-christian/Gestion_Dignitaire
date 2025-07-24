<?php
require_once __DIR__ . '/../config/database.php';

// Connexion à la base via ta fonction utilitaire
$pdo = getDatabaseConnection();

$sql = "
-- Tables de référence d'abord
-- TABLE DES ROLES
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- TABLE DES FONCTIONS (Menus principaux)
CREATE TABLE IF NOT EXISTS fonctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fonction_name VARCHAR(50) NOT NULL UNIQUE
);

-- TABLE DES SOUS-FONCTIONS (Sous-menus)
CREATE TABLE IF NOT EXISTS sousfonctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sousfonction_name VARCHAR(50) NOT NULL,
    fonction_id INT NOT NULL,
    FOREIGN KEY (fonction_id) REFERENCES fonctions(id) ON DELETE CASCADE
);


-- TABLE DES UTILISATEURS
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    nom_complet VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
  
-- TABLE DES LIENS UTILISATEURS - FONCTIONS et SOUS-FONCTIONS
-- Un utilisateur peut avoir plusieurs fonctions et sous-fonctions


-- Rôle a accès à des FONCTIONS
CREATE TABLE IF NOT EXISTS roles_fonctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    fonction_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (fonction_id) REFERENCES fonctions(id) ON DELETE CASCADE,
    UNIQUE(role_id, fonction_id)
);

-- Rôle a accès à des SOUS-FONCTIONS (sous-menus précis)
CREATE TABLE IF NOT EXISTS roles_sousfonctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    sousfonction_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (sousfonction_id) REFERENCES sousfonctions(id) ON DELETE CASCADE,
    UNIQUE(role_id, sousfonction_id)
);
CREATE TABLE IF NOT EXISTS user_fonctions (
    user_id INT NOT NULL,
    fonction_id INT NOT NULL,
    PRIMARY KEY (user_id, fonction_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (fonction_id) REFERENCES fonctions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_sousfonctions (
    user_id INT NOT NULL,
    sousfonction_id INT NOT NULL,
    PRIMARY KEY (user_id, sousfonction_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sousfonction_id) REFERENCES sousfonctions(id) ON DELETE CASCADE
);





CREATE TABLE IF NOT EXISTS domaine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS langue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL
);


-- Table region
CREATE TABLE IF NOT EXISTS region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- Table pays
CREATE TABLE IF NOT EXISTS pays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    code_iso VARCHAR(3) UNIQUE,
    indicatif VARCHAR(10) NOT NULL,
    continent VARCHAR(50),
    region_id INT DEFAULT NULL,
    CONSTRAINT fk_pays_region FOREIGN KEY (region_id) REFERENCES region(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table ville
CREATE TABLE IF NOT EXISTS ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    pays_id INT DEFAULT NULL,
    CONSTRAINT fk_pays_ville FOREIGN KEY (pays_id) REFERENCES pays(id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE IF NOT EXISTS structure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) UNIQUE NOT NULL,
    type VARCHAR(50),
    adresse TEXT,
    ville_id INT DEFAULT NULL,
    CONSTRAINT fk_structure_ville FOREIGN KEY (ville_id) 
        REFERENCES ville(id) ON DELETE SET NULL
);


CREATE TABLE IF NOT EXISTS etablissement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) UNIQUE NOT NULL,
    type VARCHAR(50),
    ville_id INT DEFAULT NULL,
    CONSTRAINT fk_etablissement_ville FOREIGN KEY (ville_id) 
        REFERENCES ville(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS entite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) UNIQUE NOT NULL,
    type VARCHAR(50),
    id_sup INT DEFAULT NULL,
    description TEXT
);


-- Table principale
CREATE TABLE IF NOT EXISTS dignitaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) UNIQUE,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    date_naissance DATE,
    lieu_naissance INT DEFAULT NULL,
    nationalite VARCHAR(100),
    genre VARCHAR(10),
    etat_civil VARCHAR(20),
    photo VARCHAR(255),
    adresse VARCHAR(255),
    telephone VARCHAR(20),
    casierJud VARCHAR(255),
    certificatsMed VARCHAR(255),
    CONSTRAINT fk_dignitaire_lieu_naissance FOREIGN KEY (lieu_naissance) 
        REFERENCES ville(id) ON DELETE SET NULL
   );

-- Décorations : indépendantes + relation N:M avec dignitaires
CREATE TABLE IF NOT EXISTS decoration (
    deco_id INT AUTO_INCREMENT PRIMARY KEY, 
    deco_nom VARCHAR(150), 
    deco_type VARCHAR(50), 
    deco_niveau VARCHAR(50), 
    deco_grade VARCHAR(50), 
    deco_date_obtention DATE, 
    deco_autorite VARCHAR(50), 
    deco_motif VARCHAR(50), 
    deco_description VARCHAR(255), 
    deco_fichierAttestation VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS pv (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(50) UNIQUE NOT NULL,
    date DATE NOT NULL,
    description TEXT
);

";

try {
    $pdo->exec($sql);
    echo "Migration 001_create_base_tables exécutée avec succès\n";
} catch (PDOException $e) {
    echo "Erreur lors de la migration : " . $e->getMessage() . "\n";
}
