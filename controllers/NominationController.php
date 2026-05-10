<?php


use classes\Nomination;
use classes\NominationDAO;

require_once __DIR__ . '/../classes/NominationDAO.class.php';
require_once __DIR__ . '/../classes/Nomination.class.php';

class NominationController
{
    public function afficherListe()
    {
        $dao = new NominationDAO();
        $nominations = $dao->findAll();
        require __DIR__ . '/../views/dashboard_nomination.view.php';
    }

    public function afficherDetail($id)
    {
        $dao = new NominationDAO();
        $nomination = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_nomination.view.php';
    }

    public function afficherFormulaireAjout()
    {
        require __DIR__ . '/../views/dashboard_nomination.view.php';
    }

    public function ajouter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomination = new Nomination(
                null,
                $_POST['dignitaire_id'],
                $_POST['entite_id'] ?? null,
                $_POST['poste_id'] ?? null,
                $_POST['pv_id'] ?? null,
                $_POST['date_debut'],
                $_POST['date_fin'] ?? null,
                $_POST['fonction'] ?? null
            );
            $dao = new NominationDAO();
            $dao->create($nomination);
            header('Location: index.php?controller=nomination&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id)
    {
        $dao = new NominationDAO();
        $nomination = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_nomination.view.php';
    }

    public function modifier($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomination = new Nomination(
                $id,
                $_POST['dignitaire_id'],
                $_POST['entite_id'] ?? null,
                $_POST['poste_id'] ?? null,
                $_POST['pv_id'] ?? null,
                $_POST['date_debut'],
                $_POST['date_fin'] ?? null,
                $_POST['fonction'] ?? null
            );
            $dao = new NominationDAO();
            $dao->update($nomination);
            header('Location: index.php?controller=nomination&action=afficherListe');
            exit;
        }
    }

    public function supprimer($id)
    {
        $dao = new NominationDAO();
        $dao->delete($id);
        header('Location: index.php?controller=nomination&action=afficherListe');
        exit;
    }
}
