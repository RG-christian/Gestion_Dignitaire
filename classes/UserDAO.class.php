<?php
require_once __DIR__ . '/../config/database.php';
require_once 'Admin.class.php';

class UserDAO {
    private ?PDO $pdo;

    public function __construct() {
        $this->pdo = getDatabaseConnection();
    }


    public function getByUsername($username): ?Admin
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Admin($row['id'], $row['username'], $row['password'], $row['nom_complet']);
        }
        return null;
    }

    public function getFonctionsByUserId($userId)
    {
        $sql = "SELECT f.id, f.fonction_name
            FROM user_fonctions uf
            JOIN fonctions f ON uf.fonction_id = f.id
            WHERE uf.user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSousFonctionsByUserId($userId)
    {
        $sql = "SELECT sf.id, sf.sousfonction_name, sf.fonction_id
            FROM user_sousfonctions usf
            JOIN sousfonctions sf ON usf.sousfonction_id = sf.id
            WHERE usf.user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getRoleNameByUserId($userId) {
        $sql = "SELECT r.role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['role_name'] : null;
    }



    // pour crer un utilisateur

    public function getAllRoles() {
        $sql = "SELECT id, role_name FROM roles";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllFonctions() {
        $sql = "SELECT id, fonction_name FROM fonctions";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSousfonctions() {
        $sql = "SELECT sf.id, sf.sousfonction_name, sf.fonction_id, f.fonction_name
                FROM sousfonctions sf
                JOIN fonctions f ON sf.fonction_id = f.id";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUser($username, $nom_complet, $password, $email, $role_id) {
        $sql = "INSERT INTO users (username, nom_complet, password, email, role_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $nom_complet, $password, $email, $role_id]);
        return $this->pdo->lastInsertId();
    }

    public function assignFonctionsToRole($role_id, $fonction_ids) {
        foreach ($fonction_ids as $fid) {
            $sql = "INSERT IGNORE INTO roles_fonctions (role_id, fonction_id) VALUES (?, ?)";
            $this->pdo->prepare($sql)->execute([$role_id, $fid]);
        }
    }

    public function assignSousfonctionsToRole($role_id, $sousfonction_ids) {
        foreach ($sousfonction_ids as $sfid) {
            $sql = "INSERT IGNORE INTO roles_sousfonctions (role_id, sousfonction_id) VALUES (?, ?)";
            $this->pdo->prepare($sql)->execute([$role_id, $sfid]);
        }
    }

    // Vérifier si l'utilisateur existe déjà (par username ou email)
    public function userExists($username, $email): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE username = ? OR email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function assignFonctionsToUser($user_id, $fonction_ids) {
        foreach ($fonction_ids as $fid) {
            $sql = "INSERT IGNORE INTO user_fonctions (user_id, fonction_id) VALUES (?, ?)";
            $this->pdo->prepare($sql)->execute([$user_id, $fid]);
        }
    }
    public function assignSousfonctionsToUser($user_id, $sousfonction_ids) {
        foreach ($sousfonction_ids as $sfid) {
            $sql = "INSERT IGNORE INTO user_sousfonctions (user_id, sousfonction_id) VALUES (?, ?)";
            $this->pdo->prepare($sql)->execute([$user_id, $sfid]);
        }
    }


    public function getAllUsersWithRole() {
        $sql = "SELECT u.id, u.username, u.nom_complet, u.email, r.role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.id DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsersWithRights() {
        $sql = "
        SELECT 
            u.id,       
            u.username, 
            u.nom_complet, 
            u.email, 
            r.role_name,
            -- Fonctions
            (SELECT GROUP_CONCAT(f.fonction_name SEPARATOR ', ')
                FROM user_fonctions uf
                JOIN fonctions f ON uf.fonction_id = f.id
                WHERE uf.user_id = u.id
            ) AS fonctions,
            -- Sous-fonctions
            (SELECT GROUP_CONCAT(sf.sousfonction_name SEPARATOR ', ')
                FROM user_sousfonctions usf
                JOIN sousfonctions sf ON usf.sousfonction_id = sf.id
                WHERE usf.user_id = u.id
            ) AS sousfonctions
        FROM users u
        JOIN roles r ON u.role_id = r.id
        ORDER BY u.id DESC
    ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateUserWithPassword($id, $username, $nom_complet, $email, $role_id, $hash) {
        $sql = "UPDATE users SET username=?, nom_complet=?, email=?, role_id=?, password=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $nom_complet, $email, $role_id, $hash, $id]);
    }

    public function updateUserWithoutPassword($id, $username, $nom_complet, $email, $role_id) {
        $sql = "UPDATE users SET username=?, nom_complet=?, email=?, role_id=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $nom_complet, $email, $role_id, $id]);
    }

    public function deleteUserFonctions($user_id) {
        $sql = "DELETE FROM user_fonctions WHERE user_id=?";
        $this->pdo->prepare($sql)->execute([$user_id]);
    }

    public function deleteUserSousfonctions($user_id) {
        $sql = "DELETE FROM user_sousfonctions WHERE user_id=?";
        $this->pdo->prepare($sql)->execute([$user_id]);
    }

    public function deleteUser($user_id) {
        $sql = "DELETE FROM users WHERE id=?";
        $this->pdo->prepare($sql)->execute([$user_id]);
    }







}
