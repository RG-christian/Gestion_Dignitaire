<?php
namespace controllers;

use classes\Enfant;

require_once __DIR__ . '/../classes/EnfantDAO.class.php';
require_once __DIR__ . '/../classes/Enfant.class.php';

class EnfantController
{
    public function afficherListe() {
        $dao = new EnfantDAO();
        $enfants = $dao->findAll();
        require __DIR__ . '/../views/dashboard_enfant.view.php';
    }

    public function afficherDetail($id) {
        $dao = new EnfantDAO();
        $enfant = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_enfant.view.php';
    }

    public function afficherFormulaireAjout() {
        require __DIR__ . '/../views/dashboard_enfant.view.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enfant = new Enfant(
                null,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['date_naiss'],
                $_POST['lieu_naiss'],
                $_POST['genre']
            );
            $dao = new EnfantDAO();
            $dao->create($enfant);
            header('Location: index.php?entite=enfant&action=liste');
            exit;
        }
    }

    public function afficherFormulaireModification($id) {
        $dao = new EnfantDAO();
        $enfant = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_enfant.view.php';
    }

    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enfant = new Enfant(
                $id,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['date_naiss'],
                $_POST['lieu_naiss'],
                $_POST['genre']
            );
            $dao = new EnfantDAO();
            $dao->update($enfant);
            header('Location: index.php?entite=enfant&action=liste');
            exit;
        }
    }

    public function supprimer($id) {
        $dao = new EnfantDAO();
        $dao->delete($id);
        header('Location: index.php?entite=enfant&action=liste');
        exit;
    }
}
?>
