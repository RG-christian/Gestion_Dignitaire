<?php


use classes\Diplome;
use classes\DiplomeDAO;

require_once __DIR__ . '/../classes/DiplomeDAO.class.php';
require_once __DIR__ . '/../classes/Diplome.class.php';

class DiplomeController
{
    public function afficherListe() {
        $dao = new DiplomeDAO();
        $diplomes = $dao->findAll();
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function afficherDetail($id) {
        $dao = new DiplomeDAO();
        $diplome = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function afficherFormulaireAjout() {
        require __DIR__ . '/../views/dashboard_diplome.view.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $diplome = new Diplome(
                null,
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
            $dao->create($diplome);
            header('Location: index.php?controller=diplome&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id) {
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

    public function supprimer($id) {
        $dao = new DiplomeDAO();
        $dao->delete($id);
        header('Location: index.php?controller=diplome&action=afficherListe');
        exit;
    }
}
?>
