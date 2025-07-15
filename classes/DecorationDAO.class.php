<?php
namespace classes;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Decoration.class.php';

class DecorationDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll()
    {
        $sql = "SELECT * FROM decoration";
        $stmt = $this->pdo->query($sql);
        $decorations = [];
        while ($row = $stmt->fetch()) {
            $decorations[] = new Decoration(
                $row['deco_id'],
                $row['deco_nom'],
                $row['deco_type'],
                $row['deco_niveau'],
                $row['deco_grade'],
                $row['deco_date_obtention'],
                $row['deco_autorite'],
                $row['deco_motif'],
                $row['deco_description'],
                $row['deco_fichierAttestation']
            );
        }
        return $decorations;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM decoration WHERE deco_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Decoration(
                $row['deco_id'],
                $row['deco_nom'],
                $row['deco_type'],
                $row['deco_niveau'],
                $row['deco_grade'],
                $row['deco_date_obtention'],
                $row['deco_autorite'],
                $row['deco_motif'],
                $row['deco_description'],
                $row['deco_fichierAttestation']
            );
        }
        return null;
    }

    public function create(Decoration $decoration)
    {
        $sql = "INSERT INTO decoration (
                    deco_nom, deco_type, deco_niveau, deco_grade, deco_date_obtention,
                    deco_autorite, deco_motif, deco_description, deco_fichierAttestation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $decoration->getNom(),
            $decoration->getType(),
            $decoration->getNiveau(),
            $decoration->getGrade(),
            $decoration->getDateObtention(),
            $decoration->getAutorite(),
            $decoration->getMotif(),
            $decoration->getDescription(),
            $decoration->getFichierAttestation()
        ]);
    }

    public function update(Decoration $decoration)
    {
        $sql = "UPDATE decoration SET
                    deco_nom = ?, deco_type = ?, deco_niveau = ?, deco_grade = ?,
                    deco_date_obtention = ?, deco_autorite = ?, deco_motif = ?,
                    deco_description = ?, deco_fichierAttestation = ?
                WHERE deco_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $decoration->getNom(),
            $decoration->getType(),
            $decoration->getNiveau(),
            $decoration->getGrade(),
            $decoration->getDateObtention(),
            $decoration->getAutorite(),
            $decoration->getMotif(),
            $decoration->getDescription(),
            $decoration->getFichierAttestation(),
            $decoration->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM decoration WHERE deco_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM decoration_dignitaire";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }


}
