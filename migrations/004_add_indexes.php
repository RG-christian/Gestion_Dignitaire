<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getDatabaseConnection();

$sql = "
-- Index pour améliorer les performances des recherches

-- Index sur les tables principales
CREATE INDEX IF NOT EXISTS idx_dignitaire_nom ON dignitaire(nom);
CREATE INDEX IF NOT EXISTS idx_dignitaire_prenom ON dignitaire(prenom);
CREATE INDEX IF NOT EXISTS idx_dignitaire_matricule ON dignitaire(matricule);
CREATE INDEX IF NOT EXISTS idx_dignitaire_nip ON dignitaire(nip);

-- Index sur les tables de liaison
CREATE INDEX IF NOT EXISTS idx_diplome_dignitaire ON diplome(dignitaire_id);
CREATE INDEX IF NOT EXISTS idx_enfants_dignitaire ON enfants(dignitaire_id);
CREATE INDEX IF NOT EXISTS idx_langues_dignitaire ON langues(dignitaire_id);
CREATE INDEX IF NOT EXISTS idx_experiences_dignitaire ON experiences(dignitaire_id);
CREATE INDEX IF NOT EXISTS idx_postes_dignitaire ON postes(dignitaire_id);
CREATE INDEX IF NOT EXISTS idx_nominations_dignitaire ON nominations(dignitaire_id);
CREATE INDEX IF NOT EXISTS idx_decoration_dignitaire_dignitaire ON decoration_dignitaire(dignitaire_id);
CREATE INDEX IF NOT EXISTS idx_decoration_dignitaire_decoration ON decoration_dignitaire(decoration_id);

-- Index sur les dates pour les recherches temporelles
CREATE INDEX IF NOT EXISTS idx_nominations_date_debut ON nominations(date_debut);
CREATE INDEX IF NOT EXISTS idx_nominations_date_fin ON nominations(date_fin);
CREATE INDEX IF NOT EXISTS idx_postes_date_debut ON postes(date_debut);
CREATE INDEX IF NOT EXISTS idx_postes_date_fin ON postes(date_fin);

-- Index sur les tables de référence
CREATE INDEX IF NOT EXISTS idx_ville_nom ON ville(nom);
CREATE INDEX IF NOT EXISTS idx_ville_pays ON ville(pays_id);
CREATE INDEX IF NOT EXISTS idx_pays_nom ON pays(nom);
CREATE INDEX IF NOT EXISTS idx_region_nom ON region(nom);
";

try {
    $pdo->exec($sql);
    echo "Migration 004_add_indexes exécutée avec succès\n";
} catch (PDOException $e) {
    echo "Erreur lors de la migration : " . $e->getMessage() . "\n";
}
