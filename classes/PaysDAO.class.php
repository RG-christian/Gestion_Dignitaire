<?php
namespace classes;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Pays.class.php';

class PaysDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function recRegion()
    {
        $st = $this->pdo->query("SELECT id, nom FROM region");
        $row = $st->fetchAll();

        return $row;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT pays.id, pays.nom, code_iso, indicatif, continent, region_id, region.id AS id_region, region.nom AS region_nom 
             FROM pays
             LEFT JOIN region ON pays.region_id = region.id
             WHERE pays.id IS NOT NULL
             ORDER BY continent, region.nom, pays.nom"
        );


        // Fetch all pays
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Pays($row['id'], $row['nom'], $row['code_iso'], $row['indicatif'], $row['continent'], $row['region_nom'], $row['region_id'], $row['id_region']);
        }
        return $results;
    }

    public function findById($id): ?Pays
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pays WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new Pays($row['id'], $row['nom'], $row['code_iso'], $row['indicatif'], $row['continent']) : null;
    }

    public function create(Pays $pays)
    {
        $stmt = $this->pdo->prepare("INSERT INTO pays (id, nom, code_iso, indicatif, continent, region_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([null, $pays->getNom(), $pays->getCodeIso(), $pays->getIndicatif(), $pays->getContinent(), $pays->getRegionId()]);
    }

    public function update(Pays $pays): bool
    {
        $stmt = $this->pdo->prepare("UPDATE pays SET nom = ?, code_iso = ?, indicatif = ?, continent = ?, region_id = ? WHERE id = ?");
        return $stmt->execute([$pays->getNom(), $pays->getCodeIso(), $pays->getIndicatif(), $pays->getContinent(), $pays->getRegionId(), $pays->getId()]);
    }

    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM pays WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
