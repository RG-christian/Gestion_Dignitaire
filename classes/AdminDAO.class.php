<?php
require_once __DIR__ . '/../config/database.php';
require_once 'Admin.class.php';

class AdminDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = getDatabaseConnection();
    }

    // Récupère un admin par username
    public function getByUsername($username): ?Admin
    {
        $sql = "SELECT * FROM admin WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();
        if ($row) {
            return new Admin($row['id'], $row['username'], $row['password'], $row['nom_complet']);
        }
        return null;
    }
}
