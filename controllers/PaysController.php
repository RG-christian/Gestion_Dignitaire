<?php

use classes\Pays;
use classes\PaysDAO;

require_once __DIR__ . '/../classes/PaysDAO.class.php';
require_once __DIR__ . '/../classes/Pays.class.php';

class PaysController
{
    public function afficherListe()
    {
        $dao = new PaysDAO();
        $pays = $dao->findAll();
        require __DIR__ . '/../views/dashboard_pays.view.php';
    }

    public function afficherDetail($id)
    {
        $dao = new PaysDAO();
        $pays = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_pays.view.php';
    }

    public function afficherFormulaireAjout()
    {
        require __DIR__ . '/../views/dashboard_pays.view.php';
    }

    public function ajouter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pays = new Pays(
                null,
                $_POST['nom'],
                $_POST['code_iso'],
                $_POST['continent']
            );
            $dao = new PaysDAO();
            $dao->create($pays);
            header('Location: index.php?controller=pays&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id)
    {
        $dao = new PaysDAO();
        $pays = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_pays.view.php';
    }

    public function modifier($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pays = new Pays(
                $id,
                $_POST['nom'],
                $_POST['code_iso'],
                $_POST['continent']
            );
            $dao = new PaysDAO();
            $dao->update($pays);
            header('Location: index.php?controller=pays&action=afficherListe');
            exit;
        }
    }

    public function supprimer($id)
    {
        $dao = new PaysDAO();
        $dao->delete($id);
        header('Location: index.php?controller=pays&action=afficherListe');
        exit;
    }
}
