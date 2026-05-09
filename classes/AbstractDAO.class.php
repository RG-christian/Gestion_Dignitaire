<?php
// classes/AbstractDAO.class.php

namespace classes;

require_once __DIR__ . '/../config/database.php';

abstract class AbstractDAO
{
    protected ?\PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    /**
     * Exécute une requête préparée et retourne le statement
     */
    protected function execute(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            if (function_exists('getLogger')) {
                getLogger()->error("Erreur SQL", [
                    'sql' => $sql,
                    'params' => $params,
                    'error' => $e->getMessage()
                ]);
            }
            throw $e;
        }
    }

    /**
     * Récupère tous les enregistrements
     */
    abstract public function findAll(): array;

    /**
     * Récupère un enregistrement par ID
     */
    abstract public function findById($id);

    /**
     * Crée un nouvel enregistrement
     */
    abstract public function create($entity): bool;

    /**
     * Met à jour un enregistrement
     */
    abstract public function update($entity): bool;

    /**
     * Supprime un enregistrement
     */
    public function delete($id): bool
    {
        $tableName = $this->getTableName();
        $sql = "DELETE FROM $tableName WHERE id = ?";
        return $this->execute($sql, [$id])->rowCount() > 0;
    }

    /**
     * Compte le nombre total d'enregistrements
     */
    public function countAll(): int
    {
        $tableName = $this->getTableName();
        $sql = "SELECT COUNT(*) as total FROM $tableName";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Retourne le nom de la table (à implémenter dans les classes filles)
     */
    abstract protected function getTableName(): string;

    /**
     * Commence une transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Valide une transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Annule une transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}
