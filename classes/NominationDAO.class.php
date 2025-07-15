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
                $row['id'],
                $row['dignitaire_id'],
                $row['date_nomination'],
                $row['pv_id'],
                $row['entite_id'],
                $row['poste_id']
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
                $row['id'],
                $row['dignitaire_id'],
                $row['date_nomination'],
                $row['pv_id'],
                $row['entite_id'],
                $row['poste_id']
            );
        }
        return null;
    }

    public function create(Nomination $nom)
    {
        $sql = "INSERT INTO nominations (dignitaire_id, date_nomination, pv_id, entite_id, poste_id)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $nom->getDignitaireId(),
            $nom->getDateNomination(),
            $nom->getPvId(),
            $nom->getEntiteId(),
            $nom->getPosteId()
        ]);
    }

    public function update(Nomination $nom)
    {
        $sql = "UPDATE nominations SET dignitaire_id = ?, date_nomination = ?, pv_id = ?, entite_id = ?, poste_id = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $nom->getDignitaireId(),
            $nom->getDateNomination(),
            $nom->getPvId(),
            $nom->getEntiteId(),
            $nom->getPosteId(),
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
                $row['id'],
                $row['dignitaire_id'],
                $row['date_nomination'],
                $row['pv_id'],
                $row['entite_id'],
                $row['poste_id']
            );
        }
        return $nominations;
    }
}