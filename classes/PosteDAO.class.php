
<?php

use classes\Poste;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Poste.class.php';


class PosteDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM postes";
        $stmt = $this->pdo->query($sql);
        $postes = [];
        while ($row = $stmt->fetch()) {
            $postes[] = new Poste(
                $row['id'], $row['dignitaire_id'], $row['intitule'], $row['ville_id'],
                $row['date_debut'], $row['date_fin'], $row['entite_id']
            );
        }
        return $postes;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM postes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Poste(
                $row['id'], $row['dignitaire_id'], $row['intitule'], $row['ville_id'],
                $row['date_debut'], $row['date_fin'], $row['entite_id']
            );
        }
        return null;
    }

    public function create(Poste $p)
    {
        $sql = "INSERT INTO postes (dignitaire_id, intitule, ville_id, date_debut, date_fin, entite_id)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $p->getDignitaireId(),
            $p->getTitre(),
            $p->getVilleId(),
            $p->getDateDebut(),
            $p->getDateFin(),
            $p->getEntiteId()
        ]);
    }

    public function update(Poste $p)
    {
        $sql = "UPDATE postes SET dignitaire_id = ?, intitule = ?, ville_id = ?, date_debut = ?, date_fin = ?, entite_id = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $p->getDignitaireId(),
            $p->getTitre(),
            $p->getVilleId(),
            $p->getDateDebut(),
            $p->getDateFin(),
            $p->getEntiteId(),
            $p->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM postes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM postes";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

}
