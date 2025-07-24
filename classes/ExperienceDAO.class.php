<?php

namespace classes;

use PDO;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Experience.class.php';
require_once __DIR__ . '/Structure.class.php';

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

        $dateDebut = empty($e->getDateDebut()) ? null : $e->getDateDebut();
        $dateFin   = empty($e->getDateFin())   ? null : $e->getDateFin();

        return $stmt->execute([
            $e->getDignitaireId(),
            $e->getIntitule(),
            $dateDebut,
            $dateFin,
            $e->getStructureId()
        ]);
    }

    public function update(Experience $e): bool
    {
        $sql = "UPDATE experiences 
                SET dignitaire_id = ?, intitule = ?, date_debut = ?, date_fin = ?, structure_id = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);

        $dateDebut = empty($e->getDateDebut()) ? null : $e->getDateDebut();
        $dateFin   = empty($e->getDateFin())   ? null : $e->getDateFin();

        return $stmt->execute([
            $e->getDignitaireId(),
            $e->getIntitule(),
            $dateDebut,
            $dateFin,
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

    public function findByDignitaireId($dignitaireId): array
    {
        $sql = "SELECT e.*, s.nom AS structure_nom 
            FROM experiences e
            LEFT JOIN structure s ON e.structure_id = s.id
            WHERE e.dignitaire_id = ?
            ORDER BY e.date_debut DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dignitaireId]);

        $experiences = [];

        while ($row = $stmt->fetch()) {
            $exp = new Experience(
                $row['id'],
                $row['dignitaire_id'],
                $row['intitule'],
                $row['date_debut'],
                $row['date_fin'],
                $row['structure_id']
            );
            $exp->setStructureNom($row['structure_nom']);
            $experiences[] = $exp;
        }

        return $experiences;
    }

    public function findByNomDignitaire(string $nom): array
    {
        $sql = "SELECT e.*, s.nom AS structure_nom
                FROM experiences e
                JOIN dignitaire d ON e.dignitaire_id = d.id
                LEFT JOIN structure s ON e.structure_id = s.id
                WHERE d.nom LIKE :nom
                ORDER BY e.date_debut DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nom' => '%' . $nom . '%']);

        $experiences = [];

        while ($row = $stmt->fetch()) {
            $exp = new Experience(
                $row['id'],
                $row['dignitaire_id'],
                $row['intitule'],
                $row['date_debut'],
                $row['date_fin'],
                $row['structure_id']
            );
            $exp->setStructureNom($row['structure_nom']);
            $experiences[] = $exp;
        }

        return $experiences;
    }
}
