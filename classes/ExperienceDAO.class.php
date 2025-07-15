<?php

namespace classes;

require_once __DIR__ . '/config/database.php';
require_once 'Experience.class.php' . __DIR__;

class ExperienceDAO
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM experiences";
        $stmt = $this->pdo->query($sql);
        $experiences = [];
        while ($row = $stmt->fetch()) {
            $experiences[] = new Experience(
                $row['id'], $row['dignitaire_id'], $row['intitule'],
                $row['date_debut'], $row['date_fin'], $row['structure_id']
            );
        }
        return $experiences;
    }

    public function findById($id): ?Experience
    {
        $sql = "SELECT * FROM experiences WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Experience(
                $row['id'], $row['dignitaire_id'], $row['intitule'],
                $row['date_debut'], $row['date_fin'], $row['structure_id']
            );
        }
        return null;
    }

    public function create(Experience $e): bool
    {
        $sql = "INSERT INTO experiences (dignitaire_id, intitule, date_debut, date_fin, structure_id)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $e->getDignitaireId(),
            $e->getIntitule(),
            $e->getDateDebut(),
            $e->getDateFin(),
            $e->getStructureId()
        ]);
    }

    public function update(Experience $e)
    {
        $sql = "UPDATE experiences SET dignitaire_id = ?, intitule = ?, date_debut = ?, date_fin = ?, structure_id = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $e->getDignitaireId(),
            $e->getIntitule(),
            $e->getDateDebut(),
            $e->getDateFin(),
            $e->getStructureId(),
            $e->getId()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM experiences WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
