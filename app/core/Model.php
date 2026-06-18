<?php

/**
 * Base Model
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

abstract class Model
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ──────────────────────────────────────────
    // Basic CRUD helpers
    // ──────────────────────────────────────────

    /**
     * Find all records
     */
    public function all(string $orderBy = '', string $direction = 'ASC'): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy !== '') {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find record by primary key
     */
    public function find(int|string $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find records by condition
     */
    public function where(string $column, mixed $value, string $operator = '='): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    /**
     * Find one record by condition
     */
    public function findWhere(string $column, mixed $value): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    /**
     * Insert a new record
     * Returns the last inserted ID
     */
    public function insert(array $data): string|false
    {
        $data = $this->addTimestamps($data, true);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Update a record by primary key
     */
    public function update(int|string $id, array $data): bool
    {
        $data = $this->addTimestamps($data, false);
        $set  = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * Delete a record by primary key
     */
    public function delete(int|string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Count all records
     */
    public function count(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table}");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Paginate results
     */
    public function paginate(int $page = 1, int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count();

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Execute a raw query
     */
    public function query(string $sql, array $bindings = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Execute a raw statement (INSERT/UPDATE/DELETE)
     */
    public function execute(string $sql, array $bindings = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($bindings);
    }

    // ──────────────────────────────────────────
    // Internal helpers
    // ──────────────────────────────────────────

    private function addTimestamps(array $data, bool $isInsert): array
    {
        $now = date('Y-m-d H:i:s');
        if ($isInsert && !isset($data['created_at'])) {
            $data['created_at'] = $now;
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = $now;
        }
        return $data;
    }
}
