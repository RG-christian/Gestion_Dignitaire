<?php
namespace classes;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Ville.class.php';

class VilleDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }


    public function recPays()
    {
        $st = $this->pdo->query("SELECT id, nom FROM pays");
        $row = $st->fetchAll();

        return $row;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT ville.id, ville.nom, pays.nom AS nom_pays
            FROM ville 
            JOIN pays ON ville.pays_id = pays.id"
        );
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new Ville($row['id'], $row['nom'], $row['nom_pays']);
        }
        return $results;
    }

    public function findById($id): ?Ville
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ville WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new Ville($row['id'], $row['nom'], $row['pays_id']) : null;
    }

    public function create(Ville $ville): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO ville (nom, pays_id) VALUES (?, ?)");
        return $stmt->execute([$ville->getNom(), $ville->getPaysId()]);
    }

    public function update(Ville $ville): bool
    {
        $stmt = $this->pdo->prepare("UPDATE ville SET nom = ?, pays_id = ? WHERE id = ?");
        return $stmt->execute([$ville->getNom(), $ville->getPaysId(), $ville->getId()]);
    }

    public function delete($id): bool
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
