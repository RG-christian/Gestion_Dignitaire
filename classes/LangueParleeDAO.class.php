<?php

namespace classes;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/LangueParlee.class.php';

class LangueParleeDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll()
    {
        $sql = "SELECT * FROM langues";
        $stmt = $this->pdo->query($sql);
        $langues = [];
        while ($row = $stmt->fetch()) {
            $langues[] = new LangueParlee(
                $row['id'], $row['dignitaire_id'], $row['langue_id'], $row['niveau']
            );
        }
        return $langues;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM langues WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new LangueParlee(
                $row['id'], $row['dignitaire_id'], $row['langue_id'], $row['niveau']
            );
        }
        return null;
    }

    public function create(LangueParlee $l): bool
    {
        $sql = "INSERT INTO langues (dignitaire_id, langue_id, niveau)
                VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $l->getDignitaireId(),
            $l->getLangueId(),
            $l->getNiveau()
        ]);
    }

    public function update(LangueParlee $l): bool
    {
        $sql = "UPDATE langues SET dignitaire_id = ?, langue_id = ?, niveau = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $l->getDignitaireId(),
            $l->getLangueId(),
            $l->getNiveau(),
            $l->getId()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM langues WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
