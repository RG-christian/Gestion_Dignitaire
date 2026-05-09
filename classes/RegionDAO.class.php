<?php
namespace classes;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Region.class.php';

class RegionDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM region");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Region($row['id'], $row['nom'], null);
        }
        return $results;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM region WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new Region($row['id'], $row['nom'], null) : null;
    }

    public function create(Region $region)
    {
        $stmt = $this->pdo->prepare("INSERT INTO region (nom) VALUES (?)");
        return $stmt->execute([$region->getNom()]);
    }

    public function update(Region $region)
    {
        $stmt = $this->pdo->prepare("UPDATE region SET nom = ? WHERE id = ?");
        return $stmt->execute([$region->getNom(), $region->getId()]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM region WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) as total FROM region";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
