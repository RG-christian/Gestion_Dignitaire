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
    etablissement_id INT,
    annee VARCHAR(10),
    ville_id INT,
    domaine_id INT,
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
    lieu_naissance INT,
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
        REFERENCES code_langue(id) ON DELETE CASCADE
);

-- Expériences professionnelles
CREATE TABLE IF NOT EXISTS experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    intitule VARCHAR(150),
    date_debut DATE,
    date_fin DATE,
    structure_id INT,
    CONSTRAINT fk_experience_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_experience_structure FOREIGN KEY (structure_id) 
        REFERENCES structure(id) ON DELETE SET NULL
);

-- Postes occupés
CREATE TABLE IF NOT EXISTS postes (
    i d INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    intitule VARCHAR(150),
    date_debut DATE,
    date_fin DATE,
    entite_id INT,
    CONSTRAINT fk_postes_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_postes_entite FOREIGN KEY (entite_id) 
        REFERENCES entite(id) ON DELETE SET NULL
);

-- Nominations officielles
CREATE TABLE IF NOT EXISTS nominations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id INT NOT NULL,
    date_nomination DATE,
    pv_id INT, -- à définir si table PV existe
    entite_id INT,
    poste_id INT,
    CONSTRAINT fk_nomination_dignitaire FOREIGN KEY (dignitaire_id) 
        REFERENCES dignitaire(id) ON DELETE CASCADE,
    CONSTRAINT fk_nomination_entite FOREIGN KEY (entite_id) 
        REFERENCES entite(id) ON DELETE SET NULL,
    CONSTRAINT fk_nomination_poste FOREIGN KEY (poste_id) 
        REFERENCES postes(id) ON DELETE SET NULL
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
