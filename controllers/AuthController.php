<?php
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/validator.php';
require_once __DIR__ . '/../config/logger.php';
require_once __DIR__ . '/../classes/UserDAO.class.php';

use classes\UserDAO;

class AuthController
{
    public function index() {
        $this->login();
    }

    public function login()
    {
        secureSession();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Vérification du token CSRF
            if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
                $error = "Token de sécurité invalide";
                getLogger()->warning("Tentative de connexion avec token CSRF invalide", [
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            } else {
                // Validation des données
                $validator = new Validator($_POST);
                $validator->required('username', 'Le nom d\'utilisateur est requis')
                         ->required('password', 'Le mot de passe est requis');

                if (!$validator->isValid()) {
                    $error = "Veuillez remplir tous les champs";
                } else {
                    $username = Validator::sanitize($_POST['username']);
                    $password = $_POST['password'];

                    $dao = new UserDAO();
                    $admin = $dao->getByUsername($username);

                    if ($admin && password_verify($password, $admin->getPassword())) {
                        // Régénération de l'ID de session après login (protection contre fixation)
                        session_regenerate_id(true);

                        $_SESSION['admin_id'] = $admin->getId();
                        $_SESSION['admin_username'] = $admin->getUsername();
                        $_SESSION['admin_nom_complet'] = $admin->getNomComplet();

                        $roleName = $dao->getRoleNameByUserId($admin->getId());
                        $_SESSION['role_name'] = $roleName;

                        $fonctions = $dao->getFonctionsByUserId($admin->getId());
                        $sousfonctions = $dao->getSousFonctionsByUserId($admin->getId());
                        $_SESSION['fonctions'] = $fonctions;
                        $_SESSION['sousfonctions'] = $sousfonctions;

                        getLogger()->info("Connexion réussie", [
                            'user_id' => $admin->getId(),
                            'username' => $username
                        ]);

                        header('Location: index.php?controller=dignitaire&action=afficherListe');
                        exit;
                    } else {
                        $error = "Nom d'utilisateur ou mot de passe incorrect";
                        getLogger()->warning("Tentative de connexion échouée", [
                            'username' => $username,
                            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                        ]);
                    }
                }
            }
        }

        include __DIR__ . '/../views/login.php';
    }

    public function dashboard()
    {
        secureSession();
        requireAuth();
        include __DIR__ . '/../views/dashboard_dignitaire.view.php';
    }

    public function logout()
    {
        secureSession();
        
        if (isset($_SESSION['admin_id'])) {
            getLogger()->info("Déconnexion", [
                'user_id' => $_SESSION['admin_id']
            ]);
        }

        session_unset();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        header('Location: index.php?controller=auth&action=index');
        exit;
    }
}
