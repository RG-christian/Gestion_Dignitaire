<?php


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Ville.class.php';

class VilleDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM ville");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Ville($row['id'], $row['nom'], $row['region_id']);
        }
        return $results;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ville WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new Ville($row['id'], $row['nom'], $row['region_id']) : null;
    }

    public function create(Ville $ville)
    {
        $stmt = $this->pdo->prepare("INSERT INTO ville (nom, region_id) VALUES (?, ?)");
        return $stmt->execute([$ville->getNom(), $ville->getRegionId()]);
    }

    public function update(Ville $ville)
    {
        $stmt = $this->pdo->prepare("UPDATE ville SET nom = ?, region_id = ? WHERE id = ?");
        return $stmt->execute([$ville->getNom(), $ville->getRegionId(), $ville->getId()]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM ville WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM ville";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }


}
