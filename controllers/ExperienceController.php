<?php

// Importation des classes nécessaires
use classes\DignitaireDAO;
use classes\Experience;
use classes\ExperienceDAO;
use classes\StructureDAO;

// Inclusion des fichiers de classe requis
require_once __DIR__ . '/../classes/Experience.class.php';
require_once __DIR__ . '/../classes/ExperienceDAO.class.php';
require_once __DIR__ . '/../classes/Structure.class.php';
require_once __DIR__ . '/../classes/StructureDAO.class.php';

class ExperienceController
{

    //  Liste des expériences
    public function afficherListe($id)
    {
        $dao = new ExperienceDAO();
        $experiences = [];

        // Vérifie si un ID de dignitaire est fourni
        if (!empty($id)) {
            // Si une recherche par nom est faite
            if (isset($_GET['recherche']) && !empty(trim($_GET['recherche']))) {
                $recherche = htmlspecialchars(trim($_GET['recherche']));
                $experiences = $dao->findByNomDignitaire($recherche);
            } else {
                // Sinon, on liste les expériences par ID du dignitaire
                $experiences = $dao->findByDignitaireId($id);
            }
        }
        $structures = [];

        // Charge la vue avec la variable $experiences
        ob_start();
        require __DIR__ . '/../views/dashboard_experience.view.php';
        return ob_get_clean();
    }


    // 2. Formulaire d’ajout d’expérience

    public function addForm($id)
    {
        // On récupère toutes les structures disponibles pour remplir la liste déroulante
        $structureDao = new StructureDAO();
        $structures = $structureDao->findAll();

        // On charge la vue du formulaire d'ajout
        ob_start();
        require __DIR__ . '/../views/experience_add.view.php';
        return ob_get_clean();
    }


    // 3. Traitement du formulaire d’ajout

    public function saveNew($id)
    {
        // Vérifie que le formulaire a bien été soumis en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Création d’une nouvelle expérience avec les données envoyées
            $exp = new Experience();
            $exp->setDignitaireId($id);
            $exp->setIntitule($_POST['poste']);
            $exp->setStructureId($_POST['institution']);
            $exp->setDateDebut($_POST['date_debut']);
            $exp->setDateFin($_POST['date_fin']);

            // Insertion dans la base de données
            $dao = new ExperienceDAO();
            $dao->create($exp);

            // Redirection vers la liste des expériences du dignitaire
            header("Location: index.php?controller=experience&action=listByDignitaire&id=$id");
            exit();
        }
    }


    // 4. Formulaire de modification d’expérience

    public function editForm($id)
    {
        // On récupère l'expérience à modifier
        $dao = new ExperienceDAO();
        $experience = $dao->findById($id);

        // Et toutes les structures pour la liste déroulante
        $structureDao = new StructureDAO();
        $structures = $structureDao->findAll();

        // On charge la vue du formulaire d’édition
        ob_start();
        require __DIR__ . '/../views/experience_edit.view.php';
        return ob_get_clean();
    }


    // 5. Traitement du formulaire de modification

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dao = new ExperienceDAO();
            // On récupère l’objet à modifier
            $exp = $dao->findById($id);

            // Mise à jour des propriétés avec les nouvelles données
            $exp->setIntitule($_POST['poste']);
            $exp->setStructureId($_POST['institution']);
            $exp->setDateDebut($_POST['date_debut']);
            $exp->setDateFin($_POST['date_fin']);

            // Enregistrement en base
            $dao->update($exp);

            // Redirection vers la liste des expériences du dignitaire concerné
            header("Location: index.php?controller=experience&action=listByDignitaire&id=" . $exp->getDignitaireId());
            exit();
        }
    }


    // 6. Suppression d’expérience

    public function delete($id)
    {
        $dao = new ExperienceDAO();
        // On récupère l’expérience avant de la supprimer pour connaître l’ID du dignitaire
        $exp = $dao->findById($id);
        $dao->delete($id);

        // Redirection vers la liste après suppression
        header("Location: index.php?controller=experience&action=listByDignitaire&id=" . $exp->getDignitaireId());
        exit();
    }



}
