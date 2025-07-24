<?php

use classes\Experience;
use classes\ExperienceDAO;
use classes\StructureDAO;
use classes\DignitaireDAO;
use JetBrains\PhpStorm\NoReturn;

// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../classes/Experience.class.php';
require_once __DIR__ . '/../classes/ExperienceDAO.class.php';
require_once __DIR__ . '/../classes/Structure.class.php';
require_once __DIR__ . '/../classes/StructureDAO.class.php';
require_once __DIR__ . '/../classes/Dignitaire.class.php';
require_once __DIR__ . '/../classes/DignitaireDAO.class.php';

class ExpérienceController
{
    // 1. Liste des expériences
    public function listByDignitaire($id): bool|string
    {
        $dao = new ExperienceDAO();
        $experiences = [];
        $dignitaireDAO = new DignitaireDAO();
        $dignitaires = $dignitaireDAO->findAll();

        // Recherche par nom
        if (isset($_GET['recherche_nom']) && !empty(trim($_GET['recherche_nom']))) {
            $nom = htmlspecialchars(trim($_GET['recherche_nom']));
            $dignitaire = $dignitaireDAO->findByNom($nom);
            if ($dignitaire) {
                $experiences = $dao->findByDignitaireId($dignitaire->getId());
            }
        } elseif (!empty($id)) {
            $experiences = $dao->findByDignitaireId($id);
        }

        ob_start();
        // Juste avant require
        extract([
            'experiences' => $experiences,
            'dignitaires' => $dignitaires ?? []
        ]);

        require __DIR__ . '/../views/dashboard_experience.view.php';
        return ob_get_clean();
    }

    // 2. Formulaire d’ajout
    public function addForm($id): bool|string
    {
        $structureDao = new StructureDAO();
        $structures = $structureDao->findAll();

        ob_start();
        require __DIR__ . '/../views/experience_add.view.php';
        return ob_get_clean();
    }

    // 3. Traitement de l’ajout
    public function saveNew($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $exp = new Experience();
            $exp->setDignitaireId($id);
            $exp->setIntitule($_POST['poste']);
            $exp->setStructureId($_POST['institution']);
            $exp->setDateDebut($_POST['date_debut']);
            $exp->setDateFin($_POST['date_fin']);

            $dao = new ExperienceDAO();
            $dao->create($exp);

            header("Location: index.php?controller=experience&action=listByDignitaire&id=$id");
            exit();
        }
    }

    // 4. Formulaire d’édition
    public function editForm($id): bool|string
    {
        $dao = new ExperienceDAO();
        $experience = $dao->findById($id);

        $structureDao = new StructureDAO();
        $structures = $structureDao->findAll();

        ob_start();
        require __DIR__ . '/../views/experience_edit.view.php';
        return ob_get_clean();
    }

    // 5. Mise à jour
    public function update($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dao = new ExperienceDAO();
            $exp = $dao->findById($id);

            $exp->setIntitule($_POST['poste']);
            $exp->setStructureId($_POST['institution']);
            $exp->setDateDebut($_POST['date_debut']);
            $exp->setDateFin($_POST['date_fin']);

            $dao->update($exp);

            header("Location: index.php?controller=experience&action=listByDignitaire&id=" . $exp->getDignitaireId());
            exit();
        }
    }

    // 6. Suppression
    #[NoReturn] public function delete($id): void
    {
        $dao = new ExperienceDAO();
        $exp = $dao->findById($id);
        $dao->delete($id);

        header("Location: index.php?controller=experience&action=listByDignitaire&id=" . $exp->getDignitaireId());
        exit();
    }

    // 7. Autocomplétion AJAX (optionnelle si tu veux passer en JS)
    public function autocompleteNoms()
    {
        $dao = new DignitaireDAO();
        $noms = $dao->getAllNomsPrenoms();

        header('Content-Type: application/json');
        echo json_encode($noms);
        exit();
    }
}
