<?php
namespace classes;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Nomination.class.php';

class NominationDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll()
    {
        $sql = "SELECT * FROM nominations";
        $stmt = $this->pdo->query($sql);
        $nominations = [];
        while ($row = $stmt->fetch()) {
            $nominations[] = new Nomination(
                $row['id'] ?? null,
                $row['dignitaire_id'] ?? null,
                $row['entite_id'] ?? null,
                $row['poste_id'] ?? null,
                $row['pv_id'] ?? null,
                $row['date_debut'] ?? null,
                $row['date_fin'] ?? null,
                $row['fonction'] ?? null
            );
        }
        return $nominations;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM nominations WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Nomination(
                $row['id'] ?? null,
                $row['dignitaire_id'] ?? null,
                $row['entite_id'] ?? null,
                $row['poste_id'] ?? null,
                $row['pv_id'] ?? null,
                $row['date_debut'] ?? null,
                $row['date_fin'] ?? null,
                $row['fonction'] ?? null
            );
        }
        return null;
    }

    public function create(Nomination $nom)
    {
        $sql = "INSERT INTO nominations (dignitaire_id, entite_id, poste_id, pv_id, date_debut, date_fin, fonction)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $nom->getDignitaireId(),
            $nom->getEntiteId(),
            $nom->getPosteId(),
            $nom->getPvId(),
            $nom->getDateDebut(),
            $nom->getDateFin(),
            $nom->getFonction()
        ]);
    }

    public function update(Nomination $nom)
    {
        $sql = "UPDATE nominations SET dignitaire_id = ?, entite_id = ?, poste_id = ?, pv_id = ?, date_debut = ?, date_fin = ?, fonction = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $nom->getDignitaireId(),
            $nom->getEntiteId(),
            $nom->getPosteId(),
            $nom->getPvId(),
            $nom->getDateDebut(),
            $nom->getDateFin(),
            $nom->getFonction(),
            $nom->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM nominations WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function findByDignitaireId($dignitaire_id)
    {
        $sql = "SELECT * FROM nominations WHERE dignitaire_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dignitaire_id]);
        $nominations = [];
        while ($row = $stmt->fetch()) {
            $nominations[] = new Nomination(
                $row['id'] ?? null,
                $row['dignitaire_id'] ?? null,
                $row['entite_id'] ?? null,
                $row['poste_id'] ?? null,
                $row['pv_id'] ?? null,
                $row['date_debut'] ?? null,
                $row['date_fin'] ?? null,
                $row['fonction'] ?? null
            );
        }
        return $nominations;
    }
}