<?php

use classes\LangueParlee;
use classes\LangueParleeDAO;
use JetBrains\PhpStorm\NoReturn;

require_once __DIR__ . '/../classes/LangueParleeDAO.class.php';
require_once __DIR__ . '/../classes/LangueParlee.class.php';

class LanguesController
{
    public function afficherListe(): void
    {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDatabaseConnection();
        $langues = $pdo->query("SELECT * FROM langue")->fetchAll(PDO::FETCH_ASSOC); // $langues au lieu de $result
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }



    public function afficherDetail($id): void
    {
        $dao = new LangueParleeDAO();
        $langue = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }

    public function afficherFormulaireAjout(): void
    {
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }

    public function ajouter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['langue']) && !empty(trim($_POST['langue']))) {
            require_once __DIR__ . '/../config/database.php';
            $pdo = getDatabaseConnection();
            $nom = ucfirst(strtolower(trim($_POST['langue'])));
            $stmt = $pdo->prepare("INSERT INTO langue (nom) VALUES (:nom)");
            $stmt->execute(['nom' => $nom]);
            header("Location: index.php?controller=langues&action=afficherListe");
            exit();
        }
    }


    public function afficherFormulaireModification($id): void
    {
        $dao = new LangueParleeDAO();
        $langue = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }

    public function modifier($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $langue = new LangueParlee(
                $id,
                $_POST['dignitaire_id'],
                $_POST['langue_id'],
                $_POST['niveau']
            );
            $dao = new LangueParleeDAO();
            $dao->update($langue);
            header("Location: index.php?controller=langues&action=afficherListe");
            exit;
        }
    }

    #[NoReturn] public function supprimer($id): void
    {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("DELETE FROM langue WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: index.php?controller=langues&action=afficherListe");
        exit();
    }
}
