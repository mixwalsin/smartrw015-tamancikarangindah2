<?php

/**
 * UserModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class UserModel extends Model
{
    protected string $table = 'users';

    /**
     * SQL base untuk JOIN dengan roles.
     */
    private function baseSelect(): string
    {
        return "SELECT u.*, r.slug AS role_slug, r.name AS role_name
                FROM users u
                LEFT JOIN roles r ON r.id = u.role_id";
    }

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare(
            $this->baseSelect() . " WHERE u.username = ? LIMIT 1"
        );
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        if ($row) {
            $row['role'] = $row['role_slug'] ?? '';
        }
        return $row;
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            $this->baseSelect() . " WHERE u.email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row) {
            $row['role'] = $row['role_slug'] ?? '';
        }
        return $row;
    }

    /**
     * Ambil semua user beserta nama role.
     */
    public function allWithRole(string $orderBy = 'u.name', string $direction = 'ASC'): array
    {
        $stmt = $this->db->prepare(
            $this->baseSelect() . " ORDER BY {$orderBy} {$direction}"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateLastLogin(int $id): bool
    {
        return $this->execute(
            "UPDATE {$this->table} SET last_login_at = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $id]
        );
    }

    public function getActiveUsers(): array
    {
        return $this->where('is_active', 1);
    }

    public function getByRole(string $role): array
    {
        return $this->where('role', $role);
    }
}
