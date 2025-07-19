<?php

namespace classes; // Déclaration du namespace

use PDO; // Import de la classe PDO pour la base de données

// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../config/database.php'; // Connexion DB
require_once __DIR__ . '/Experience.class.php';    // Classe Experience
require_once __DIR__ . '/Structure.class.php';     // Classe Structure (utile pour les jointures)

class ExperienceDAO
{
    // Propriété contenant la connexion PDO
    private ?PDO $pdo;

    // Constructeur : initialise la connexion à la base de données
    public function __construct()
    {
        $this->pdo = getDatabaseConnection(); // Appel à une fonction dans `database.php`
    }

    // Récupère toutes les expériences de la table
    public function findAll(): array
    {
        $sql = "SELECT * FROM experiences";
        $stmt = $this->pdo->query($sql); // Exécution directe car pas de paramètre
        $experiences = [];

        while ($row = $stmt->fetch()) {
            // Pour chaque ligne, on crée un objet Experience
            $experiences[] = new Experience(
                $row['id'], $row['dignitaire_id'], $row['intitule'],
                $row['date_debut'], $row['date_fin'], $row['structure_id']
            );
        }

        return $experiences;
    }

    // Récupère une seule expérience à partir de son ID
    public function findById($id): ?Experience
    {
        $sql = "SELECT * FROM experiences WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $row = $stmt->fetch();

        // Si une ligne est trouvée, on crée l'objet Experience
        if ($row) {
            return new Experience(
                $row['id'], $row['dignitaire_id'], $row['intitule'],
                $row['date_debut'], $row['date_fin'], $row['structure_id']
            );
        }

        return null; // Sinon on retourne null
    }

    // Insère une nouvelle expérience dans la base
    public function create(Experience $e): bool
    {
        $sql = "INSERT INTO experiences (dignitaire_id, intitule, date_debut, date_fin, structure_id)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        // Récupère les dates depuis l'objet et remplace les chaînes vides par null
        $dateDebut = empty($e->getDateDebut()) ? null : $e->getDateDebut();
        $dateFin   = empty($e->getDateFin())   ? null : $e->getDateFin();

        // Exécution de la requête avec les paramètres
        return $stmt->execute([
            $e->getDignitaireId(),
            $e->getIntitule(),
            $dateDebut,
            $dateFin,
            $e->getStructureId()
        ]);
    }

    // Met à jour une expérience existante
    public function update(Experience $e)
    {
        $sql = "UPDATE experiences 
                SET dignitaire_id = ?, intitule = ?, date_debut = ?, date_fin = ?, structure_id = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);

        // Gestion des champs de date pour éviter les erreurs SQL
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

    // Supprime une expérience de la base
    public function delete($id): bool
    {
        $sql = "DELETE FROM experiences WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Récupère toutes les expériences liées à un dignitaire
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
            // On injecte manuellement le nom de la structure (non présent dans le constructeur)
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
