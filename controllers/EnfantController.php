<?php


use classes\DignitaireDAO;
use classes\Enfant;
use JetBrains\PhpStorm\NoReturn;


require_once __DIR__ . '/../classes/EnfantDAO.class.php';
require_once __DIR__ . '/../classes/Enfant.class.php';
require_once __DIR__ . '/../classes/DignitaireDAO.class.php';
require_once __DIR__ . '/../classes/Dignitaire.class.php';

class EnfantController
{
    public function afficherListe(): void
    {
    $dao = new EnfantDAO();
    $enfants = $dao->findAll();

    $daoDignitaire = new DignitaireDAO();
    $dignitaires = $daoDignitaire->findAll();

    require __DIR__ . '/../views/dashboard_enfant.view.php';
}




    public function afficherDetail($id): void
    {
        $daoEnfant = new EnfantDAO();
        $enfant = $daoEnfant->findById($id);

        if (!$enfant) {
            echo "Enfant non trouvé.";
            exit;
        }

        $daoDignitaire = new DignitaireDAO();
        $dignitaire = $daoDignitaire->findById($enfant->getDignitaireId());

        $enfantsDuDignitaire = $daoEnfant->findByDignitaireId($dignitaire->getId());

        require __DIR__ . '/../views/dashboard_enfant.view.php';
    }

    public function afficherFormulaireAjout(): void
    {
        $daoDignitaire = new DignitaireDAO();
        $dignitaires = $daoDignitaire->findAll();

        require __DIR__ . '/../views/dashboard_enfant.view.php';
    }

    public function ajouter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['dignitaire_id'])) {
                die("Veuillez choisir un dignitaire pour l’enfant.");
            }
            $enfant = new Enfant(
                null,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['date_naissance'],
                $_POST['lieu_naissance'],
                $_POST['genre']
            );
            $enfant->setDignitaireId($_POST['dignitaire_id']);
            $dao = new EnfantDAO();
            $dao->create($enfant);
            header('Location: index.php?controller=enfant&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id): void
    {
        $dao = new EnfantDAO();
        $enfant = $dao->findById($id);

        $daoDignitaire = new \classes\DignitaireDAO();
        $dignitaires = $daoDignitaire->findAll();

        require __DIR__ . '/../views/dashboard_enfant.view.php';
    }

    public function modifier($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enfant = new Enfant(
                $id,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['date_naissance'],
                $_POST['lieu_naissance'],
                $_POST['genre']
            );

            if(isset($_POST['dignitaire_id'])){
                $enfant->setDignitaireId($_POST['dignitaire_id']);
            }

            $dao = new EnfantDAO();
            $dao->update($enfant);
            header('Location: index.php?controller=enfant&action=afficherListe');
            exit;
        }
    }

    #[NoReturn] public function supprimer($id): void
    {
        $dao = new EnfantDAO();
        $dao->delete($id);
        header('Location: index.php?controller=enfant&action=afficherListe');
        exit;
    }
}