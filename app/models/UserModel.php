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
        return $this->findWhere('username', $username);
    }

    public function findByEmail(string $email): array|false
    {
        return $this->findWhere('email', $email);
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
