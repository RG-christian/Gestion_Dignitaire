<?php
session_start();
require_once __DIR__ . '/../classes/UserDAO.class.php';

class AuthController
{
    // Affiche le formulaire et traite la connexion

    public function index() {
        $this->login();

    }

    public function login()

{
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $dao = new UserDAO();
        $admin = $dao->getByUsername($username);

        if ($admin && password_verify($password, $admin->getPassword())) {
            $_SESSION['admin_id'] = $admin->getId();
            $_SESSION['admin_username'] = $admin->getUsername();
            $_SESSION['admin_nom_complet'] = $admin->getNomComplet();

            // AJOUT ICI (AVANT la redirection) :
            $roleName = $dao->getRoleNameByUserId($admin->getId());
            $_SESSION['role_name'] = $roleName;

            $fonctions = $dao->getFonctionsByUserId($admin->getId());
            $sousfonctions = $dao->getSousFonctionsByUserId($admin->getId());
            $_SESSION['fonctions'] = $fonctions;
            $_SESSION['sousfonctions'] = $sousfonctions;

            header('Location: index.php?controller=dignitaire&action=afficherListe');
            exit;
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect";
        }
    }

    // Affiche la vue de login (on transmet l'erreur si besoin)
    include __DIR__ . '/../views/login.php';
}

    // Affiche le tableau de bord après connexion
    public function dashboard()
    {
        if (!isset($_SESSION['admin_id'])) {
            // Si non connecté, retour à la connexion
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        include __DIR__ . '/../views/dashboard_dignitaire.view.php';
    }




    // Déconnexion propre
    public function logout()
    {
        session_start();
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
