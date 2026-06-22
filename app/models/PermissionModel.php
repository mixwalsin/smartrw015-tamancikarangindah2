<?php

/**
 * PermissionModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PermissionModel extends Model
{
    protected string $table = 'permissions';

    /**
     * Ambil semua permissions dikelompokkan per modul.
     */
    public function allGrouped(): array
    {
        $rows    = $this->all('modul', 'ASC');
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['modul']][] = $row;
        }
        return $grouped;
    }

    /**
     * Ambil permissions beserta daftar roles yang memilikinya.
     */
    public function allWithRoles(): array
    {
        return $this->query("
            SELECT p.*,
                   GROUP_CONCAT(r.name ORDER BY r.id SEPARATOR ', ') AS roles
            FROM permissions p
            LEFT JOIN role_permissions rp ON rp.permission_id = p.id
            LEFT JOIN roles r ON r.id = rp.role_id
            GROUP BY p.id
            ORDER BY p.modul ASC, p.id ASC
        ");
    }

    /**
     * Cek apakah slug sudah digunakan.
     */
    public function isSlugTaken(string $slug, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM permissions WHERE slug = ? AND id <> ?"
        );
        $stmt->execute([$slug, $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Ambil daftar modul yang tersedia.
     */
    public function getModuls(): array
    {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT modul FROM permissions ORDER BY modul ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Override insert – tidak ada updated_at pada tabel permissions.
     */
    public function insert(array $data): string|false
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        unset($data['updated_at']);
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt         = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Override update – tidak ada updated_at pada tabel permissions.
     */
    public function update(int|string $id, array $data): bool
    {
        unset($data['updated_at'], $data['created_at']);
        $set  = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([...array_values($data), $id]);
    }
}
