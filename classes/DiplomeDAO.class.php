<?php
// classes/DiplomeDAO.class.php

namespace classes;


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Diplome.class.php';

class DiplomeDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll()
    {
        $sql = "SELECT * FROM diplome";
        $stmt = $this->pdo->query($sql);
        $diplomes = [];
        while ($row = $stmt->fetch()) {
            $diplomes[] = new Diplome(
                $row['id'], $row['dignitaire_id'], $row['intitule'], $row['etablissement'],
                $row['annee'], $row['ville_id'], $row['domaine_id'], $row['code'], $row['type']
            );
        }
        return $diplomes;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM diplome WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Diplome(
                $row['id'], $row['dignitaire_id'], $row['intitule'], $row['etablissement'],
                $row['annee'], $row['ville_id'], $row['domaine_id'], $row['code'], $row['type']
            );
        }
        return null;
    }

    public function create(Diplome $d)
    {
        $sql = "INSERT INTO diplome
            (dignitaire_id, intitule, etablissement, annee, ville_id, domaine_id, code, type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $d->getDignitaireId(),
            $d->getIntitule(),
            $d->getEtablissement(),
            $d->getAnnee(),
            $d->getVilleId(),
            $d->getDomaineId(),
            $d->getCode(),
            $d->getType()
        ]);
    }

    public function update(Diplome $d)
    {
        $sql = "UPDATE diplome SET
            dignitaire_id = ?, intitule = ?, etablissement = ?, annee = ?, ville_id = ?, domaine_id = ?, code = ?, type = ?
            WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $d->getDignitaireId(),
            $d->getIntitule(),
            $d->getEtablissement(),
            $d->getAnnee(),
            $d->getVilleId(),
            $d->getDomaineId(),
            $d->getCode(),
            $d->getType(),
            $d->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM diplome WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

}
