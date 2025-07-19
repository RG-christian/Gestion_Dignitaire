<?php
require_once __DIR__ . '/../classes/UserDAO.class.php';

class AdminController
{

    public function create()
    {
        session_start();

        // Vérifie si l'utilisateur est superadmin
        if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'Superadmin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $dao = new UserDAO();
        $users = $dao->getAllUsersWithRights();
        $roles = $dao->getAllRoles();
        $fonctions = $dao->getAllFonctions();
        $sousfonctions = $dao->getAllSousfonctions();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // AJAX ?
            $isAjax = (isset($_POST['ajax']) && $_POST['ajax'] == '1') || (
                    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
                );

            $username = trim($_POST['username']);
            $nom_complet = trim($_POST['nom_complet']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role_id = intval($_POST['role_id']);
            $fonction_ids = $_POST['fonctions'] ?? [];
            $sousfonction_ids = $_POST['sousfonctions'] ?? [];

            // Vérifier les champs obligatoires
            if ($dao->userExists($username, $email)) {
                $error = "Un utilisateur avec ce nom ou cet email existe déjà.";
            } elseif (strlen($password) < 6) {
                $error = "Le mot de passe doit contenir au moins 6 caractères.";
            } elseif (empty($fonction_ids) || empty($sousfonction_ids)) {
                $error = "Veuillez attribuer au moins une fonction et une sous-fonction.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $user_id = $dao->addUser($username, $nom_complet, $hash, $email, $role_id);
                $dao->assignFonctionsToUser($user_id, $fonction_ids);
                $dao->assignSousfonctionsToUser($user_id, $sousfonction_ids);

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => "Utilisateur ajouté avec succès !"]);
                } else {
                    // Rediriger après succès
                    header('Location: index.php?controller=admin&action=liste&created=1');
                }
                exit;
            }

            // En AJAX : renvoyer l'erreur sans afficher la vue
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error]);
                exit;
            }
        }

        // Affichage du formulaire
        include __DIR__ . '/../views/admin_create.php';
    }

    public function edit()
    {
        session_start();
        if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'Superadmin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $dao = new UserDAO();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['edit_user_id']);
            $username = trim($_POST['edit_username']);
            $nom_complet = trim($_POST['edit_nom_complet']);
            $email = trim($_POST['edit_email']);
            $role_id = intval($_POST['edit_role_id']);
            $password = $_POST['edit_password'];
            $fonction_ids = $_POST['edit_fonctions'] ?? [];
            $sousfonction_ids = $_POST['edit_sousfonctions'] ?? [];

            // Met à jour les infos de base
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $dao->updateUserWithPassword($user_id, $username, $nom_complet, $email, $role_id, $hash);
            } else {
                $dao->updateUserWithoutPassword($user_id, $username, $nom_complet, $email, $role_id);
            }

            // MAJ des droits individuels
            $dao->deleteUserFonctions($user_id);
            $dao->deleteUserSousfonctions($user_id);
            $dao->assignFonctionsToUser($user_id, $fonction_ids);
            $dao->assignSousfonctionsToUser($user_id, $sousfonction_ids);

            header('Location: index.php?controller=admin&action=create&updated=1');
            exit;
        } else {
            // Optionnel : Pré-charger les infos pour l'affichage dans un vrai formulaire séparé
            header('Location: index.php?controller=admin&action=create');
            exit;
        }
    }

    public function delete()
    {
        session_start();
        if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'Superadmin') {
            header('Location: index.php?controller=auth&action=login');

            exit;
        }

        $dao = new UserDAO();

        $user_id = intval($_GET['id'] ?? 0);
        if ($user_id > 0) {
            $dao->deleteUserFonctions($user_id);
            $dao->deleteUserSousfonctions($user_id);
            $dao->deleteUser($user_id);
        }

        header('Location: index.php?controller=admin&action=create&deleted=1');
        exit;
    }

    public function ajaxList() {
        $dao = new UserDAO();
        $users = $dao->getAllUsersWithRights();
        include __DIR__ . '/../views/users_table_partial.php'; // Un fichier qui génère juste le <table> et tbody
        exit;
    }




}
