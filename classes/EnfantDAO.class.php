<?php

use classes\Enfant;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Enfant.class.php';

class EnfantDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function getAll(): bool|array
    {
        $sql = "SELECT e.*, d.nom AS nom_dignitaire, d.prenom AS prenom_dignitaire
            FROM enfants e
            LEFT JOIN dignitaire d ON e.dignitaire_id = d.id
            ORDER BY e.nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function findAll(): array
    {
        $sql = "SELECT * FROM enfants";
        $stmt = $this->pdo->query($sql);
        $enfants = [];
        while ($row = $stmt->fetch()) {
            $enfants[] = new Enfant(
                $row['id'], $row['dignitaire_id'],  $row['nom'], $row['prenom'],
                $row['date_naissance'], $row['lieu_naissance'], $row['genre'] ?? null // Ajout du dignitaire_id ici
            );
        }
        return $enfants;
    }

    public function findById($id): ?Enfant
    {
        $sql = "SELECT * FROM enfants WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Enfant(
                $row['id'], $row['nom'], $row['prenom'],
                $row['date_naissance'], $row['lieu_naissance'], $row['genre'],
                $row['dignitaire_id'] ?? null
            );
        }
        return null;
    }

    public function findByDignitaireId($dignitaireId): array
    {
        $sql = "SELECT * FROM enfants WHERE dignitaire_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dignitaireId]);

        $enfants = [];
        while ($row = $stmt->fetch()) {
            $enfants[] = new Enfant(
                $row['id'], $row['nom'], $row['prenom'],
                $row['date_naissance'], $row['lieu_naissance'], $row['genre'],
                $row['dignitaire_id'] ?? null
            );
        }
        return $enfants;
    }

    public function create(Enfant $e): bool
    {
        $sql = "INSERT INTO enfants (dignitaire_id, nom, prenom, date_naissance, lieu_naissance, genre)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $e->getDignitaireId(),
            $e->getNom(),
            $e->getPrenom(),
            $e->getDateNaissance(),
            $e->getLieuNaiss(),
            $e->getGenre(),

        ]);
    }

    public function update(Enfant $e): bool
    {
        $sql = "UPDATE enfants SET nom = ?, prenom = ?, date_naissance = ?, lieu_naissance = ?, genre = ?, dignitaire_id = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $e->getNom(),
            $e->getPrenom(),
            $e->getDateNaissance(),
            $e->getLieuNaiss(),
            $e->getGenre(),
            $e->getDignitaireId(),
            $e->getId()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM enfants WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}