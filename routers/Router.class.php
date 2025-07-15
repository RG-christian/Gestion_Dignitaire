<?php
namespace routers;

use ReflectionMethod;

class Router {
    public function handleRequest() {
        $controller = $_GET['controller'] ?? 'Auth';
        $action = $_GET['action'] ?? 'index';


        $id = $_GET['id'] ?? null;

        $controllerFile = "controllers/" . ucfirst($controller) . "Controller.php";

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerClass = ucfirst($controller) . "Controller";
            $ctrl = new $controllerClass();

            // Vérifie si la méthode existe dans le contrôleur
            if (method_exists($ctrl, $action)) {
                $ref = new ReflectionMethod($ctrl, $action);
                $nbParams = $ref->getNumberOfParameters();
                if ($nbParams == 0) {
                    // -------- Correction : on retourne la vue --------
                    return $ctrl->$action();
                } else {
                    return $ctrl->$action($id);
                }
            } else {
                // Action inconnue : affiche une page d'erreur ou redirige
                return "<div class='alert alert-danger'>Action inconnue !</div>";
            }
        } else {
            // Contrôleur inconnu : affiche une page d'erreur ou redirige
            return "<div class='alert alert-danger'>Contrôleur non trouvé !</div>";
        }
    }
}
