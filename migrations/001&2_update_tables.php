<?php
require_once __DIR__ . '/../config/database.php';
$pdo = getDatabaseConnection();

$table = 'entite';

// 1. Ajoute la colonne ville_id si elle n'existe pas déjà
$colName = 'id_sup';
$addColSql = "ALTER TABLE $table ADD COLUMN id_sup INT DEFAULT NULL AFTER nom;";
///*
// 2.1. Ajoute la contrainte clé étrangère après la colonne
$fkName = 'fk_entite_entite'; // nom de la contrainte
$addFkSql = "ALTER TABLE $table 
    ADD CONSTRAINT $fkName
    FOREIGN KEY (id_sup) REFERENCES entite(id) ON DELETE SET NULL;";

//// 2. Ajoute la contrainte clé étrangère après la colonne
//$fkName = 'fk_postes_ville'; // nom de la contrainte
//$addFkSql = "ALTER TABLE $table
//    ADD CONSTRAINT $fkName
//    FOREIGN KEY (ville_id) REFERENCES ville(id) ON DELETE SET NULL;";
////*/



// Vérifie si la colonne existe déjà
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :field
");
$stmt->execute([
    'table' => $table,
    'field' => $colName
]);
$exists = $stmt->fetchColumn();

if (!$exists) {
    try {
        $pdo->exec($addColSql);
        echo "Colonne '$colName' ajoutée avec succès.\n";
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout de la colonne '$colName' : " . $e->getMessage() . "\n";
    }
} else {
    echo "Colonne '$colName' existe déjà, rien à faire.\n";
}
///*
// Vérifie si la contrainte existe déjà
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND CONSTRAINT_NAME = :fk
");
$stmt->execute([
    'table' => $table,
    'fk' => $fkName
]);
$fk_exists = $stmt->fetchColumn();

if (!$fk_exists) {
    try {
        $pdo->exec($addFkSql);
        echo "Contrainte '$fkName' ajoutée avec succès.\n";
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout de la contrainte '$fkName' : " . $e->getMessage() . "\n";
    }
} else {
    echo "Contrainte '$fkName' existe déjà, rien à faire.\n";
}
//*/
echo "Migration postes : colonne ville_id et contrainte clé étrangère OK.\n";

?>