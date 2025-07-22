<?php
require_once __DIR__ . '/../config/database.php';

// Connexion à la base via ta fonction utilitaire
$pdo = getDatabaseConnection();

$sql = "
-- Diplômes obtenus par un dignitaire
CREATE TABLE IF NOT EXISTS diplome (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    intitule VARCHAR(255),
    etablissement_id INT DEFAULT NULL, 
    annee VARCHAR(10),
    ville_id INT DEFAULT NULL,        
    domaine_id INT DEFAULT NULL,       
    code VARCHAR(30),
    type VARCHAR(30),
    CONSTRAINT fk_diplome_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_diplome_etablissement FOREIGN KEY (etablissement_id) 
        REFERENCES etablissement(id) ON DELETE SET NULL,
    CONSTRAINT fk_diplome_ville FOREIGN KEY (ville_id) 
        REFERENCES ville(id) ON DELETE SET NULL,
    CONSTRAINT fk_diplome_domaine FOREIGN KEY (domaine_id) 
        REFERENCES domaine(id) ON DELETE SET NULL
);

-- Enfants du dignitaire
CREATE TABLE IF NOT EXISTS enfants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    date_naissance DATE,
    lieu_naissance INT DEFAULT NULL, 
    genre VARCHAR(10),
    CONSTRAINT fk_enfant_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_enfant_lieu_naissance FOREIGN KEY (lieu_naissance) 
        REFERENCES ville(id) ON DELETE SET NULL
);

-- Langues parlées par un dignitaire
CREATE TABLE IF NOT EXISTS langues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    langue_id INT NOT NULL,
    niveau VARCHAR(30),
    CONSTRAINT fk_langue_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_langue_code FOREIGN KEY (langue_id) 
        REFERENCES langue(id) ON DELETE CASCADE
);

-- Expériences professionnelles
CREATE TABLE IF NOT EXISTS experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    intitule VARCHAR(150),
    date_debut DATE,
    date_fin DATE,
    structure_id INT DEFAULT NULL,
    CONSTRAINT fk_experience_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_experience_structure FOREIGN KEY (structure_id) 
        REFERENCES structure(id) ON DELETE SET NULL
);

-- Postes occupés
CREATE TABLE IF NOT EXISTS postes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    intitule VARCHAR(150),
    date_debut DATE,
    date_fin DATE,
    entite_id INT DEFAULT NULL,
    ville_id INT DEFAULT NULL,
    CONSTRAINT fk_postes_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_postes_entite FOREIGN KEY (entite_id) 
        REFERENCES entite(id) ON DELETE SET NULL,
    CONSTRAINT fk_postes_ville FOREIGN KEY (ville_id) 
        REFERENCES ville(id) ON DELETE SET NULL    
);

-- Nominations officielles
CREATE TABLE IF NOT EXISTS nominations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    entite_id INT DEFAULT NULL,
    poste_id INT DEFAULT NULL,
    pv_id INT DEFAULT NULL,
    date_debut DATE NOT NULL,
    date_fin   DATE DEFAULT NULL,
    fonction   VARCHAR(150),

    CONSTRAINT fk_nomination_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_nomination_entite FOREIGN KEY (entite_id) 
        REFERENCES entite(id) ON DELETE SET NULL,
    CONSTRAINT fk_nomination_poste FOREIGN KEY (poste_id) 
        REFERENCES postes(id) ON DELETE SET NULL,
    CONSTRAINT fk_nomination_pv FOREIGN KEY (pv_id) 
        REFERENCES pv(id) ON DELETE SET NULL
);



CREATE TABLE IF NOT EXISTS historique_nominations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomination_id INT NOT NULL,
    dignitaire_id INT NOT NULL,
    poste_id INT DEFAULT NULL,
    entite_id INT DEFAULT NULL,
    date_nomination DATE,
    date_fin DATE,
    description TEXT,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historique_nomination_nomination FOREIGN KEY (nomination_id) REFERENCES nominations(id) ON DELETE CASCADE,
    CONSTRAINT fk_historique_nomination_dignitaire FOREIGN KEY (dignitaire_id) REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_historique_nomination_poste FOREIGN KEY (poste_id) REFERENCES postes(id) ON DELETE SET NULL,
    CONSTRAINT fk_historique_nomination_entite FOREIGN KEY (entite_id) REFERENCES entite(id) ON DELETE SET NULL
);

-- Association entre dignitaires et décorations
CREATE TABLE IF NOT EXISTS decoration_dignitaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    decoration_id INT NOT NULL,
    date_attribution DATE NOT NULL,
    CONSTRAINT fk_decoration_dignitaire_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_decoration_dignitaire_decoration FOREIGN KEY (decoration_id) 
        REFERENCES decoration(deco_id) ON DELETE CASCADE
);
";

try {
    $pdo->exec($sql);
    echo "Migration 002_create_tables exécutée avec succès\n";
} catch (PDOException $e) {
    echo "Erreur lors de la migration : " . $e->getMessage() . "\n";
}
