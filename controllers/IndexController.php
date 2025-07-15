<?php


class IndexController
{
    public function afficherAccueil()
    {
        ob_start();
        require __DIR__ . '/../views/index.view.php'; // Affiche la vue d'accueil
        return ob_get_clean();
    }
}
