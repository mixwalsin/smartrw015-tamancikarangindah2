<?php

/**
 * RoleModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class RoleModel extends Model
{
    protected string $table = 'roles';

    /**
     * Ambil semua roles beserta jumlah user dan permissions.
     */
    public function allWithStats(): array
    {
        return $this->query("
            SELECT r.*,
                   COUNT(DISTINCT u.id)  AS total_users,
                   COUNT(DISTINCT rp.permission_id) AS total_permissions
            FROM roles r
            LEFT JOIN users u  ON u.role_id  = r.id
            LEFT JOIN role_permissions rp ON rp.role_id = r.id
            GROUP BY r.id
            ORDER BY r.id ASC
        ");
    }

    /**
     * Ambil permission IDs yang dimiliki role tertentu.
     */
    public function getPermissionIds(int $roleId): array
    {
        $rows = $this->query(
            "SELECT permission_id FROM role_permissions WHERE role_id = ?",
            [$roleId]
        );
        return array_column($rows, 'permission_id');
    }

    /**
     * Set permissions untuk role (replace all).
     */
    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->execute("DELETE FROM role_permissions WHERE role_id = ?", [$roleId]);
        foreach ($permissionIds as $pid) {
            $this->execute(
                "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                [$roleId, (int) $pid]
            );
        }
    }

    /**
     * Ambil users yang memiliki role tertentu.
     */
    public function getUsers(int $roleId): array
    {
        return $this->query(
            "SELECT u.id, u.name, u.username, u.email, u.is_active, u.last_login_at
             FROM users u WHERE u.role_id = ? ORDER BY u.name ASC",
            [$roleId]
        );
    }

    /**
     * Cek apakah slug sudah digunakan oleh role lain.
     */
    public function isSlugTaken(string $slug, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM roles WHERE slug = ? AND id <> ?"
        );
        $stmt->execute([$slug, $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
