<?php
ob_start(); // Active la temporisation de sortie


use classes\Diplome;
use classes\DiplomeDAO;
use JetBrains\PhpStorm\NoReturn;

require_once __DIR__ . '/../classes/DiplomeDAO.class.php';
require_once __DIR__ . '/../classes/Diplome.class.php';

class DiplomeController
{
    public function afficherListe(): void
    {
        $dao = new DiplomeDAO();
        $diplomes = $dao->findAll();
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function afficherDetail($id): void
    {
        $dao = new DiplomeDAO();
        $diplome = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function afficherFormulaireAjout() {
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function afficherNombreDiplomesParDignitaire() {
        $dao = new DiplomeDAO();
        $diplomesParDignitaire = $dao->findNombreDiplomesParDignitaire();
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }


    public function afficherDetailParDignitaire($id) {
        $dao = new DiplomeDAO();
        $diplomes_dignitaire = $dao->findDiplomesByDignitaireId($id);
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function ajouter(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['intitule'])) {
            $diplomeDAO = new DiplomeDAO();
            $count = count($_POST['intitule']);

            for ($i = 0; $i < $count; $i++) {
                $diplome = new Diplome(
                    null,
                    $_POST['dignitaire_id'],
                    $_POST['intitule'][$i],
                    $_POST['etablissement'][$i],
                    $_POST['annee'][$i],
                    $_POST['ville_id'][$i],
                    $_POST['domaine_id'][$i],
                    $_POST['code'][$i],
                    $_POST['type'][$i]
                );
                $diplomeDAO->create($diplome);
            }
            header('Location: index.php?controller=diplome&action=afficherListe');
            exit;
        }
    }

    public function rechercher() {
        $motCle = $_GET['q'] ?? '';
        $dao = new DiplomeDAO();
        $diplomes = $dao->search($motCle);
        require_once 'view/dashboard_diplome.view.php';
    }



    public function afficherFormulaireModification($id): void
    {
        $dao = new DiplomeDAO();
        $diplome = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $diplome = new Diplome(
                $id,
                $_POST['dignitaire_id'],
                $_POST['intitule'],
                $_POST['etablissement'],
                $_POST['annee'],
                $_POST['ville_id'],
                $_POST['domaine_id'],
                $_POST['code'],
                $_POST['type']
            );
            $dao = new DiplomeDAO();
            $dao->update($diplome);
            header('Location: index.php?controller=diplome&action=afficherListe');
            exit;
        }
    }

    #[NoReturn] public function supprimer($id): void
    {
        $dao = new DiplomeDAO();
        $dao->delete($id);
        header('Location: index.php?controller=diplome&action=afficherListe');
        exit;
    }
}

ob_end_flush();
?>
