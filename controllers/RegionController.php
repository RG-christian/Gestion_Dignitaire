<?php


use classes\Region;
use classes\RegionDAO;

require_once __DIR__ . '/../classes/RegionDAO.class.php';
require_once __DIR__ . '/../classes/Region.class.php';

class RegionController
{
    public function afficherListe()
    {
        $dao = new RegionDAO();
        $regions = $dao->findAll();
        require __DIR__ . '/../views/dashboard_region.view.php';
    }

    public function afficherDetail($id)
    {
        $dao = new RegionDAO();
        $region = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_region.view.php';
    }

    public function afficherFormulaireAjout()
    {
        require __DIR__ . '/../views/dashboard_region.view.php';
    }

    public function ajouter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $region = new Region(
                null,
                $_POST['nom'],
                $_POST['pays_id']
            );
            $dao = new RegionDAO();
            $dao->create($region);
            header('Location: index.php?controller=region&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id)
    {
        $dao = new RegionDAO();
        $region = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_region.view.php';
    }

    public function modifier($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $region = new Region(
                $id,
                $_POST['nom'],
                $_POST['pays_id']
            );
            $dao = new RegionDAO();
            $dao->update($region);
            header('Location: index.php?controller=region&action=afficherListe');
            exit;
        }
    }

    public function supprimer($id)
    {
        $dao = new RegionDAO();
        $dao->delete($id);
        header('Location: index.php?controller=region&action=afficherListe');
        exit;
    }
}
