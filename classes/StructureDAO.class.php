<?php
namespace classes;

use PDO;
use Structure;

require_once __DIR__ . '/../config/database.php';
require_once 'Structure.class.php';

class StructureDAO
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM structure";
        $stmt = $this->pdo->query($sql);
        $structures = [];

        while ($row = $stmt->fetch()) {
            $structures[] = new Structure($row['id'], $row['nom']);
        }

        return $structures;
    }
}
