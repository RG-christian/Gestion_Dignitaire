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

    public function findAll(): array
    {
        $sql = "SELECT * FROM diplome";
        $stmt = $this->pdo->query($sql);
        $diplomes = [];
        while ($row = $stmt->fetch()) {
            $diplomes[] = new Diplome(
                $row['id'],
                $row['dignitaire_id'],
                $row['intitule'],
                $row['etablissement_id'],
                $row['annee'],
                $row['ville_id'],
                $row['domaine_id'],
                $row['code'],
                $row['type']
            );
        }
        return $diplomes;
    }

    public function findById($id): ?Diplome
    {
        $sql = "SELECT * FROM diplome WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Diplome(
                $row['id'],
                $row['dignitaire_id'],
                $row['intitule'],
                $row['etablissement'],
                $row['annee'],
                $row['ville_id'],
                $row['domaine_id'],
                $row['code'],
                $row['type']
            );
        }
        return null;
    }

    public function search($keyword): array
    {
        $sql = "SELECT * FROM diplome 
                WHERE intitule LIKE :keyword 
                OR etablissement_id LIKE :keyword 
                OR annee LIKE :keyword";

        $stmt = $this->pdo->prepare($sql);
        $searchTerm = "%" . $keyword . "%";
        $stmt->bindParam(':keyword', $searchTerm, \PDO::PARAM_STR);
        $stmt->execute();

        $diplomes = [];
        while ($row = $stmt->fetch()) {
            $diplomes[] = new Diplome(
                $row['id'],
                $row['dignitaire_id'],
                $row['intitule'],
                $row['etablissement_id'],
                $row['annee'],
                $row['ville_id'],
                $row['domaine_id'],
                $row['code'],
                $row['type']
            );
        }
        return $diplomes;
    }

    public function findNombreDiplomesParDignitaire(): bool|array
    {
        $sql = "SELECT d.dignitaire_id, dg.nom, dg.prenom, COUNT(*) AS nombre_diplomes 
                FROM diplome d 
                JOIN dignitaire dg ON d.dignitaire_id = dg.id 
                GROUP BY d.dignitaire_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findDiplomesByDignitaireId($id_dignitaire): array
    {
        $sql = "SELECT * FROM diplome WHERE dignitaire_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_dignitaire]);

        $diplomes = [];
        while ($row = $stmt->fetch()) {
            $diplomes[] = new Diplome(
                $row['id'],
                $row['dignitaire_id'],
                $row['intitule'],
                $row['etablissement_id'],
                $row['annee'],
                $row['ville_id'],
                $row['domaine_id'],
                $row['code'],
                $row['type']
            );
        }
        return $diplomes;
    }

    public function create(Diplome $d): bool
    {
        try {
            var_dump($this->pdo);
            $sql = "INSERT INTO diplome
            (dignitaire_id, intitule, etablissement_id, annee, ville_id, domaine_id, code, type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                $d->getDignitaireId(),
                $d->getIntitule(),
                $d->getEtablissement(),
                $d->getAnnee(),
                $d->getVilleId(),
                $d->getDomaineId(),
                $d->getCode(),
                $d->getType()
            ]);

            if (!$success) {
                $errorInfo = $stmt->errorInfo();
                echo "Erreur d'insertion SQL : " . $errorInfo[2]; // Affiche l’erreur SQL exacte
            }

            return $success;
        } catch (\PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return false;
        }
    }


    public function update(Diplome $d): bool
    {
        $sql = "UPDATE diplome SET
            dignitaire_id = ?, intitule = ?, etablissement_id = ?, annee = ?, ville_id = ?, domaine_id = ?, code = ?, type = ?
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

    public function delete($id): bool
    {
        $sql = "DELETE FROM diplome WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
