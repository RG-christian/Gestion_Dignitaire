<?php

use classes\Poste;



require_once __DIR__ . '/../classes/PosteDAO.class.php';
require_once __DIR__ . '/../classes/Poste.class.php';

class PosteController
{
    public function afficherListe() {
        $dao = new PosteDAO();
        $postes = $dao->findAll();
        require __DIR__ . '/../views/dashboard_poste.view.php';
    }

    public function afficherDetail($id) {
        $dao = new PosteDAO();
        $poste = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_poste.view.php';
    }

    public function afficherFormulaireAjout() {
        require __DIR__ . '/../views/dashboard_poste.view.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $poste = new Poste(
                null,
                $_POST['dignitaire_id'],
                $_POST['titre'],
                $_POST['ville_id'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['entite_id']
            );
            $dao = new PosteDAO();
            $dao->create($poste);
            header('Location: index.php?controller=poste&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id) {
        $dao = new PosteDAO();
        $poste = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_poste.view.php';
    }

    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $poste = new Poste(
                $id,
                $_POST['dignitaire_id'],
                $_POST['titre'],
                $_POST['ville_id'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['entite_id']
            );
            $dao = new PosteDAO();
            $dao->update($poste);
            header('Location: index.php?entite=poste&action=liste');
            exit;
        }
    }

    public function supprimer($id) {
        $dao = new PosteDAO();
        $dao->delete($id);
        header('Location: index.php?entite=poste&action=liste');
        exit;
    }
}

