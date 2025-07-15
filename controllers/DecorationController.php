<?php



use classes\Decoration;
use classes\DecorationDAO;

require_once __DIR__ . '/../classes/DecorationDAO.class.php';
require_once __DIR__ . '/../classes/Decoration.class.php';

class DecorationController
{
    public function afficherListe()
    {
        $dao = new DecorationDAO();
        $decorations = $dao->findAll();
        require __DIR__ . '/../views/dashboard_decoration.view.php';
    }

    public function afficherDetail($id)
    {
        $dao = new DecorationDAO();
        $decoration = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_decoration.view.php';
    }

    public function afficherFormulaireAjout()
    {
        require __DIR__ . '/../views/dashboard_decoration.view.php';
    }

    public function ajouter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $decoration = new Decoration(
                null,
                $_POST['deco_nom'],
                $_POST['deco_type'],
                $_POST['deco_niveau'],
                $_POST['deco_grade'],
                $_POST['deco_date_obtention'],
                $_POST['deco_autorite'],
                $_POST['deco_motif'],
                $_POST['deco_description'],
                $_POST['deco_fichierAttestation']
            );
            $dao = new DecorationDAO();
            $dao->create($decoration);
            header('Location: index.php?controller=decoration&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id)
    {
        $dao = new DecorationDAO();
        $decoration = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_decoration.view.php';
    }

    public function modifier($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $decoration = new Decoration(
                $id,
                $_POST['deco_nom'],
                $_POST['deco_type'],
                $_POST['deco_niveau'],
                $_POST['deco_grade'],
                $_POST['deco_date_obtention'],
                $_POST['deco_autorite'],
                $_POST['deco_motif'],
                $_POST['deco_description'],
                $_POST['deco_fichierAttestation']
            );
            $dao = new DecorationDAO();
            $dao->update($decoration);
            header('Location: index.php?controller=decoration&action=afficherListe');
            exit;
        }
    }

    public function supprimer($id)
    {
        $dao = new DecorationDAO();
        $dao->delete($id);
        header('Location: index.php?controller=decoration&action=afficherListe');
        exit;
    }
}
