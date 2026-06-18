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

    public function findByUsername(string $username): array|false
    {
        return $this->findOneWithRole('username', $username);
    }

    public function findByEmail(string $email): array|false
    {
        return $this->findWhere('email', $email);
    }

    public function findWithRoleContext(int $id): array|false
    {
        $sql = "SELECT u.*, r.slug AS role, r.name AS role_name, kk.rt_id
                FROM {$this->table} u
                LEFT JOIN roles r ON r.id = u.role_id
                LEFT JOIN warga w ON w.id = u.warga_id
                LEFT JOIN kk ON kk.id = w.kk_id
                WHERE u.id = ?
                LIMIT 1";
        $rows = $this->query($sql, [$id]);

        return $rows[0] ?? false;
    }

    public function getRtScope(int $id): ?int
    {
        $user = $this->findWithRoleContext($id);
        if (!$user) {
            return null;
        }

        return isset($user['rt_id']) ? (int) $user['rt_id'] : null;
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
        $sql = "SELECT u.*, r.slug AS role, r.name AS role_name
                FROM {$this->table} u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.is_active = 1
                ORDER BY u.name ASC";

        return $this->query($sql);
    }

    public function getByRole(string $role): array
    {
        $sql = "SELECT u.*, r.slug AS role, r.name AS role_name
                FROM {$this->table} u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE r.slug = ? OR r.name = ?
                ORDER BY u.name ASC";

        return $this->query($sql, [$role, $role]);
    }

    private function findOneWithRole(string $column, string $value): array|false
    {
        $sql = "SELECT u.*, r.slug AS role, r.name AS role_name
                FROM {$this->table} u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.{$column} = ?
                LIMIT 1";

        $rows = $this->query($sql, [$value]);

        return $rows[0] ?? false;
    }
}
