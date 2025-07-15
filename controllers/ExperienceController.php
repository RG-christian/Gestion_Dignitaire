<?php

use classes\Experience;
use classes\ExperienceDAO;

require_once __DIR__ . '/../classes/ExperienceDAO.class.php';
require_once __DIR__ . '/../classes/Experience.class.php';

class ExperienceController
{
    public function afficherListe() {
        $dao = new ExperienceDAO();
        $experiences = $dao->findAll();
        require __DIR__ . '/../views/dashboard_experience.view.php';
    }

    public function afficherDetail($id) {
        $dao = new ExperienceDAO();
        $experience = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_experience.view.php';
    }

    public function afficherFormulaireAjout() {
        require __DIR__ . '/../views/dashboard_experience.view.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $experience = new Experience(
                null,
                $_POST['dignitaire_id'],
                $_POST['intitule'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['structure_id']
            );
            $dao = new ExperienceDAO();
            $dao->create($experience);
            header('Location: index.php?entite=experience&action=liste');
            exit;
        }
    }

    public function afficherFormulaireModification($id) {
        $dao = new ExperienceDAO();
        $experience = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_experience.view.php';
    }

    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $experience = new Experience(
                $id,
                $_POST['dignitaire_id'],
                $_POST['intitule'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['structure_id']
            );
            $dao = new ExperienceDAO();
            $dao->update($experience);
            header('Location: index.php?entite=experience&action=liste');
            exit;
        }
    }

    public function supprimer($id) {
        $dao = new ExperienceDAO();
        $dao->delete($id);
        header('Location: index.php?entite=experience&action=liste');
        exit;
    }
}
?>
