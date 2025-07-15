<?php

use classes\LangueParlee;
use classes\LangueParleeDAO;

require_once __DIR__ . '/../classes/LangueParleeDAO.class.php';
require_once __DIR__ . '/../classes/LangueParlee.class.php';

class LangueParleeController
{
    public function afficherListe() {
        $dao = new LangueParleeDAO();
        $langues = $dao->findAll();
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }

    public function afficherDetail($id) {
        $dao = new LangueParleeDAO();
        $langue = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }

    public function afficherFormulaireAjout() {
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $langue = new LangueParlee(
                null,
                $_POST['dignitaire_id'],
                $_POST['langue_id'],
                $_POST['niveau']
            );
            $dao = new LangueParleeDAO();
            $dao->create($langue);
            header('Location: index.php?entite=langue_parlee&action=liste');
            exit;
        }
    }

    public function afficherFormulaireModification($id) {
        $dao = new LangueParleeDAO();
        $langue = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_langue_parlee.view.php';
    }

    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $langue = new LangueParlee(
                $id,
                $_POST['dignitaire_id'],
                $_POST['langue_id'],
                $_POST['niveau']
            );
            $dao = new LangueParleeDAO();
            $dao->update($langue);
            header('Location: index.php?entite=langue_parlee&action=liste');
            exit;
        }
    }

    public function supprimer($id) {
        $dao = new LangueParleeDAO();
        $dao->delete($id);
        header('Location: index.php?entite=langue_parlee&action=liste');
        exit;
    }
}
