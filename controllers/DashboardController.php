<?php

use classes\DignitaireDAO;
use classes\PosteDAO;
use classes\DecorationDAO;
use classes\VilleDAO;
use classes\PaysDAO;
use classes\RegionDAO;

require_once __DIR__ . '/../classes/DignitaireDAO.class.php';
require_once __DIR__ . '/../classes/PosteDAO.class.php';
require_once __DIR__ . '/../classes/DecorationDAO.class.php';
require_once __DIR__ . '/../classes/VilleDAO.class.php';
require_once __DIR__ . '/../classes/PaysDAO.class.php';
require_once __DIR__ . '/../classes/RegionDAO.class.php';

class DashboardController
{
    public function index()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['admin_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Récupérer les statistiques
        $dignitaireDAO = new DignitaireDAO();
        $posteDAO = new PosteDAO();
        $decorationDAO = new DecorationDAO();
        $villeDAO = new VilleDAO();
        $paysDAO = new PaysDAO();
        $regionDAO = new RegionDAO();

        $totalDignitaires = $dignitaireDAO->countAll();
        $totalPostes = $posteDAO->countAll();
        $totalDecorations = $decorationDAO->countAll();
        $totalVilles = $villeDAO->countAll();
        $totalPays = $paysDAO->countAll();
        $totalRegions = $regionDAO->countAll();

        // Récupérer les derniers dignitaires
        $derniersDignitaires = $dignitaireDAO->findAll();
        // Limiter à 10
        $derniersDignitaires = array_slice($derniersDignitaires, 0, 10);

        // Charger le layout
        require __DIR__ . '/../layout.php';
        
        // Afficher le contenu du dashboard
        echo '<main class="flex-1 ml-0 lg:ml-64 p-6 transition-all duration-300">';
        require __DIR__ . '/../views/dashboard_content.view.php';
        echo '</main>';
    }
}
