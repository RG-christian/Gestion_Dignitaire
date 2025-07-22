<?php
// classes/DignitaireDAO.class.php

namespace classes;


use Dignitaire;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Dignitaire.class.php';

class DignitaireDAO
{
    private ?\PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    // 1. Récupérer tous les dignitaires
    public function findAll() {
        $sql = "SELECT * FROM dignitaire";
        $stmt = $this->pdo->query($sql);
        $dignitaires = [];
        while ($row = $stmt->fetch()) {
            $dignitaires[] = new Dignitaire(
                $row['id'], $row['nom'], $row['prenom'], $row['date_naissance'],
                $row['lieu_naissance'], $row['nationalite'], $row['genre'], $row['etat_civil'],
                $row['telephone'], $row['adresse'], $row['nip'], $row['matricule'],
                $row['photo'], $row['casierJud'], $row['certificatsMed']
            );
        }
        return $dignitaires;
    }


    // 2. Récupérer un dignitaire par ID
    public function findById($id)
    {
        $sql = "SELECT * FROM dignitaire WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Dignitaire(
                $row['id'], $row['nom'], $row['prenom'], $row['date_naissance'],
                $row['lieu_naissance'], $row['nationalite'], $row['genre'], $row['etat_civil'],
                $row['tel'], $row['adresse'], $row['nip'], $row['matricule'],
                $row['photo'], $row['casierJud'], $row['certificatsMed']  );
        }
        return null;
    }

    // 3. Ajouter un dignitaire
    public function create(Dignitaire $d)
    {
        $sql = "INSERT INTO dignitaire
            (nip, matricule, nom, prenom, date_naissance, lieu_naissance, genre, etat_civil, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $d->getNip(),
            $d->getMatricule(),
            $d->getNom(),
            $d->getPrenom(),
            $d->getDateNaissance(),
            $d->getLieuNaissance(),
            $d->getGenre(),
            $d->getEtatCivil(),
            $d->getPhoto(),
            $d->getId()
        ]);
    }

    // 4. Modifier un dignitaire
    public function update(Dignitaire $d)
    {
        $sql = "UPDATE dignitaire SET
            nip = ?, matricule = ?, nom = ?, prenom = ?, date_naissance = ?, lieu_naissance = ?,
            genre = ?, etat_civil = ?, photo = ?
            WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $d->getNip(),
            $d->getMatricule(),
            $d->getNom(),
            $d->getPrenom(),
            $d->getDateNaissance(),
            $d->getLieuNaissance(),
            $d->getGenre(),
            $d->getEtatCivil(),
            $d->getPhoto(),
            $d->getId()
        ]);
    }

    // 5. Supprimer un dignitaire
    public function delete($id)
    {
        $sql = "DELETE FROM dignitaire WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    /**
     * Récupère les diplômes pour un dignitaire donné.
     */
    public function findDiplomesByDignitaireId($dignitaireId)
    {
        $sql = "SELECT * FROM diplome WHERE dignitaire_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dignitaireId]);
        $diplomes = [];
        while ($row = $stmt->fetch()) {
            // À adapter selon ta classe Diplome
            $diplomes[] = $row; // Ou new Diplome(...) si tu as la classe
        }
        return $diplomes;
    }

    /**
     * Récupère les enfants pour un dignitaire donné.
     */
    public function findEnfantsByDignitaireId($dignitaireId)
    {
        $sql = "SELECT * FROM enfants WHERE dignitaire_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dignitaireId]);
        $enfants = [];
        while ($row = $stmt->fetch()) {
            // À adapter selon ta classe Enfant
            $enfants[] = $row; // Ou new Enfant(...)
        }
        return $enfants;
    }

    public function findPostesByDignitaireId($dignitaireId)
    {
        $sql = "SELECT p.intitule, e.nom AS entite
            FROM postes p
            LEFT JOIN entite e ON p.entite_id = e.id
            WHERE p.dignitaire_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dignitaireId]);
        return $stmt->fetchAll();
    }
    public function findPostesByDignitaireId_2($dignitaireId) {
        $sql = "SELECT * FROM postes WHERE dignitaire_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dignitaireId]);
        $postes = [];
        while ($row = $stmt->fetch()) {
            $postes[] = $row; // Ou new Poste(...) selon ton modèle
        }
        return $postes;
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM ville";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }


}
