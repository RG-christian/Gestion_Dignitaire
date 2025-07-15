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

    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM pays");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Pays($row['id'], $row['nom'], $row['code_iso'], $row['continent']);
        }
        return $results;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pays WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new Pays($row['id'], $row['nom'], $row['code_iso'], $row['continent']) : null;
    }

    public function create(Pays $pays)
    {
        $stmt = $this->pdo->prepare("INSERT INTO pays (nom, code_iso, continent) VALUES (?, ?, ?)");
        return $stmt->execute([$pays->getNom(), $pays->getCodeIso(), $pays->getContinent()]);
    }

    public function update(Pays $pays)
    {
        $stmt = $this->pdo->prepare("UPDATE pays SET nom = ?, code_iso = ?, continent = ? WHERE id = ?");
        return $stmt->execute([$pays->getNom(), $pays->getCodeIso(), $pays->getContinent(), $pays->getId()]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM pays WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
