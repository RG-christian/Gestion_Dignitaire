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

    public function findAll()
    {
        $sql = "SELECT * FROM enfants";
        $stmt = $this->pdo->query($sql);
        $enfants = [];
        while ($row = $stmt->fetch()) {
            $enfants[] = new Enfant(
                $row['id'], $row['nom'], $row['prenom'],
                $row['date_naiss'], $row['lieu_naiss'], $row['genre']
            );
        }
        return $enfants;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM enfants WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Enfant(
                $row['id'], $row['nom'], $row['prenom'],
                $row['date_naiss'], $row['lieu_naiss'], $row['genre']
            );
        }
        return null;
    }

    public function create(Enfant $e)
    {
        $sql = "INSERT INTO enfants (nom, prenom, date_naiss, lieu_naiss, genre)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $e->getNom(),
            $e->getPrenom(),
            $e->getDateNaiss(),
            $e->getLieuNaiss(),
            $e->getGenre()
        ]);
    }

    public function update(Enfant $e)
    {
        $sql = "UPDATE enfants SET nom = ?, prenom = ?, date_naiss = ?, lieu_naiss = ?, genre = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $e->getNom(),
            $e->getPrenom(),
            $e->getDateNaiss(),
            $e->getLieuNaiss(),
            $e->getGenre(),
            $e->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM enfants WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
