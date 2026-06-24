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

    public function findAuthUserByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.username = ? OR u.email = ?
             LIMIT 1'
        );
        $stmt->execute([$username, $username]);
        return $stmt->fetch();
    }

    public function findByUsername(string $username): array|false
    {
        return $this->findWhere('username', $username);
    }

    public function findByEmail(string $email): array|false
    {
        return $this->findWhere('email', $email);
    }

    public function getPermissions(int $userId, int $roleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT p.slug
             FROM permissions p
             LEFT JOIN role_permissions rp ON rp.permission_id = p.id AND rp.role_id = ?
             LEFT JOIN user_permissions up ON up.permission_id = p.id AND up.user_id = ?
             WHERE rp.role_id IS NOT NULL OR up.user_id IS NOT NULL'
        );
        $stmt->execute([$roleId, $userId]);
        return array_column($stmt->fetchAll(), 'slug');
    }

    public function registerWarga(array $data): string|false
    {
        $data['role_id'] = 4;
        unset($data['role']);
        return $this->insert($data);
    }

    public function updateLastLogin(int $id): bool
    {
        return $this->execute(
            'UPDATE users SET last_login_at = ?, updated_at = ? WHERE id = ?',
            [date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
    }

    public function getActiveUsers(): array
    {
        return $this->query(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.is_active = 1
             ORDER BY u.name ASC'
        );
    }

    public function getAllWithRoles(): array
    {
        return $this->query(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug, w.nama AS nama_warga
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             LEFT JOIN warga w ON w.id = u.warga_id
             ORDER BY u.created_at DESC'
        );
    }
}
