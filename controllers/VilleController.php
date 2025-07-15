<?php


use classes\Ville;
use classes\VilleDAO;

require_once __DIR__ . '/../classes/VilleDAO.class.php';
require_once __DIR__ . '/../classes/Ville.class.php';

class VilleController
{
    public function afficherListe()
    {
        $dao = new VilleDAO();
        $villes = $dao->findAll();
        require __DIR__ . '/../views/dashboard_ville.view.php';
    }

    public function afficherDetail($id)
    {
        $dao = new VilleDAO();
        $ville = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_ville.view.php';
    }

    public function afficherFormulaireAjout()
    {
        require __DIR__ . '/../views/dashboard_ville.view.php';
    }

    public function ajouter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ville = new Ville(
                null,
                $_POST['nom'],
                $_POST['region_id']
            );
            $dao = new VilleDAO();
            $dao->create($ville);
            header('Location: index.php?controller=ville&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id)
    {
        $dao = new VilleDAO();
        $ville = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_ville.view.php';
    }

    public function modifier($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ville = new Ville(
                $id,
                $_POST['nom'],
                $_POST['region_id']
            );
            $dao = new VilleDAO();
            $dao->update($ville);
            header('Location: index.php?controller=ville&action=afficherListe');
            exit;
        }
    }

    public function supprimer($id)
    {
        $dao = new VilleDAO();
        $dao->delete($id);
        header('Location: index.php?controller=ville&action=afficherListe');
        exit;
    }
}
