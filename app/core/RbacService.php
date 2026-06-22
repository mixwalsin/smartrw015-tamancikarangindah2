<?php

/**
 * RbacService - Role Based Access Control
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Layanan terpusat untuk cek role dan permission pengguna.
 * Permission di-cache di session untuk performa optimal.
 */

declare(strict_types=1);

class RbacService
{
    private PDO $db;

    /** Cache permissions per user (runtime, bukan session) */
    private static array $runtimeCache = [];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ──────────────────────────────────────────
    // Permission Check
    // ──────────────────────────────────────────

    /**
     * Cek apakah user saat ini memiliki permission tertentu.
     */
    public function can(string $permission): bool
    {
        $permissions = $this->getPermissions();
        return in_array($permission, $permissions, true);
    }

    /**
     * Cek apakah user memiliki salah satu dari beberapa permission.
     */
    public function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cek apakah user memiliki semua permission yang diberikan.
     */
    public function canAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->can($permission)) {
                return false;
            }
        }
        return true;
    }

    // ──────────────────────────────────────────
    // Role Check
    // ──────────────────────────────────────────

    /**
     * Cek apakah user memiliki role tertentu (slug).
     */
    public function hasRole(string $roleSlug): bool
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            return false;
        }
        return ($user['role'] ?? '') === $roleSlug;
    }

    /**
     * Cek apakah user memiliki salah satu dari beberapa role.
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cek apakah user adalah Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    // ──────────────────────────────────────────
    // Permission Loading
    // ──────────────────────────────────────────

    /**
     * Muat permissions user saat ini (dari session/cache).
     * Super Admin mendapat semua permission secara otomatis.
     */
    public function getPermissions(): array
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            return [];
        }

        $userId = (int) ($user['id'] ?? 0);

        // Gunakan cache runtime jika ada
        if (isset(self::$runtimeCache[$userId])) {
            return self::$runtimeCache[$userId];
        }

        // Cek cache session + validasi TTL
        $ttl       = defined('RBAC_CACHE_TTL') ? RBAC_CACHE_TTL : 300;
        $cacheData = $_SESSION['rbac_permissions'][$userId] ?? null;
        if ($cacheData !== null && $ttl > 0) {
            $cachedAt = $_SESSION['rbac_permissions_at'][$userId] ?? 0;
            if ((time() - $cachedAt) < $ttl) {
                self::$runtimeCache[$userId] = $cacheData;
                return $cacheData;
            }
        }

        // Muat dari database
        $permissions = $this->loadPermissionsFromDb($userId);
        $_SESSION['rbac_permissions'][$userId]    = $permissions;
        $_SESSION['rbac_permissions_at'][$userId] = time();
        self::$runtimeCache[$userId]              = $permissions;

        return $permissions;
    }

    /**
     * Paksa reload permission dari database (invalidate cache).
     */
    public function refreshPermissions(?int $userId = null): void
    {
        if ($userId === null) {
            $userId = (int) (($_SESSION['user']['id'] ?? 0));
        }
        unset(
            $_SESSION['rbac_permissions'][$userId],
            $_SESSION['rbac_permissions_at'][$userId]
        );
        unset(self::$runtimeCache[$userId]);
    }

    /**
     * Load permissions dari database untuk user tertentu.
     * Menggabungkan: permissions dari role + direct user_permissions.
     */
    public function loadPermissionsFromDb(int $userId): array
    {
        // Permission via role
        $stmt = $this->db->prepare("
            SELECT DISTINCT p.slug
            FROM permissions p
            INNER JOIN role_permissions rp ON rp.permission_id = p.id
            INNER JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = ?
            UNION
            SELECT DISTINCT p2.slug
            FROM permissions p2
            INNER JOIN user_permissions up ON up.permission_id = p2.id
            WHERE up.user_id = ?
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Ambil semua permissions user dalam format array slug.
     */
    public function getUserPermissionSlugs(int $userId): array
    {
        return $this->loadPermissionsFromDb($userId);
    }

    // ──────────────────────────────────────────
    // Role Info
    // ──────────────────────────────────────────

    /**
     * Ambil semua roles dari database.
     */
    public function getAllRoles(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil semua permissions dari database, dikelompokkan per modul.
     */
    public function getAllPermissionsGrouped(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM permissions ORDER BY modul ASC, id ASC");
        $stmt->execute();
        $all  = $stmt->fetchAll();

        $grouped = [];
        foreach ($all as $p) {
            $grouped[$p['modul']][] = $p;
        }
        return $grouped;
    }

    /**
     * Ambil permission slugs yang dimiliki role tertentu.
     */
    public function getRolePermissionSlugs(int $roleId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.slug
            FROM permissions p
            INNER JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = ?
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ──────────────────────────────────────────
    // Session Update
    // ──────────────────────────────────────────

    /**
     * Update session user dengan data RBAC (role info + permissions).
     * Dipanggil setelah login atau perubahan role.
     */
    public function buildUserSession(array $userRow): array
    {
        return [
            'id'        => $userRow['id'],
            'name'      => $userRow['name'],
            'username'  => $userRow['username'],
            'email'     => $userRow['email'],
            'role_id'   => $userRow['role_id'],
            'role'      => $userRow['role_slug'] ?? $userRow['role'] ?? '',
            'role_name' => $userRow['role_name'] ?? '',
        ];
    }
}
