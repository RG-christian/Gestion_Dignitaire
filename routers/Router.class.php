<?php
namespace routers;

use ReflectionMethod;

class Router {
    // Whitelist des contrôleurs autorisés
    private const ALLOWED_CONTROLLERS = [
        'Auth',
        'Admin',
        'Dashboard',
        'Decoration',
        'Dignitaire',
        'Diplome',
        'Enfant',
        'Expérience',
        'Index',
        'Langues',
        'Nomination',
        'Pays',
        'Poste',
        'Region',
        'Test',
        'Ville'
    ];

    public function handleRequest() {
        $controller = $_GET['controller'] ?? 'Auth';
        $action = $_GET['action'] ?? 'index';
        $id = $_GET['id'] ?? null;

        // Validation du contrôleur (whitelist)
        $controller = ucfirst($controller);
        if (!in_array($controller, self::ALLOWED_CONTROLLERS, true)) {
            $this->handleError("Contrôleur non autorisé");
            return;
        }

        // Validation de l'action (alphanumérique uniquement)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $action)) {
            $this->handleError("Action invalide");
            return;
        }

        $controllerFile = "controllers/{$controller}Controller.php";

        if (!file_exists($controllerFile)) {
            $this->handleError("Contrôleur non trouvé");
            return;
        }

        require_once $controllerFile;
        $controllerClass = $controller . "Controller";

        if (!class_exists($controllerClass)) {
            $this->handleError("Classe de contrôleur introuvable");
            return;
        }

        $ctrl = new $controllerClass();

        if (!method_exists($ctrl, $action)) {
            $this->handleError("Action inconnue");
            return;
        }

        try {
            $ref = new ReflectionMethod($ctrl, $action);
            $nbParams = $ref->getNumberOfParameters();
            
            if ($nbParams == 0) {
                return $ctrl->$action();
            } else {
                // Validation de l'ID si présent
                if ($id !== null && !is_numeric($id)) {
                    $this->handleError("ID invalide");
                    return;
                }
                return $ctrl->$action($id);
            }
        } catch (\Exception $e) {
            // Logger l'erreur
            if (function_exists('getLogger')) {
                getLogger()->error("Erreur dans le routeur", [
                    'controller' => $controller,
                    'action' => $action,
                    'error' => $e->getMessage()
                ]);
            }
            $this->handleError("Une erreur est survenue");
        }
    }

    private function handleError(string $message): void
    {
        http_response_code(404);
        echo "<div class='alert alert-danger'>$message</div>";
    }
}
