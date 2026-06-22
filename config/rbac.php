<?php

/**
 * Konfigurasi RBAC – Role Based Access Control
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

// Cache permission di session selama N detik
// Set 0 untuk tidak ada cache (selalu baca dari DB)
define('RBAC_CACHE_TTL', 300);  // 5 menit

// Permission default untuk pengguna yang login tanpa role terdefinisi
define('RBAC_DEFAULT_PERMISSIONS', []);

// Role yang tidak dapat dihapus
define('RBAC_PROTECTED_ROLES', ['super_admin', 'warga']);

// Daftar semua role yang didukung beserta display label
define('RBAC_ROLES', [
    'super_admin'       => 'Super Admin',
    'ketua_rw'          => 'Ketua RW',
    'sekretaris_rw'     => 'Sekretaris RW',
    'bendahara_rw'      => 'Bendahara RW',
    'ketua_rt'          => 'Ketua RT',
    'admin_rt'          => 'Admin RT',
    'petugas_posyandu'  => 'Petugas Posyandu',
    'petugas_security'  => 'Petugas Security',
    'warga'             => 'Warga',
]);

// Hierarki akses menu (slug => permission yang dibutuhkan)
define('RBAC_MENU_PERMISSIONS', [
    'dashboard'        => null,               // semua yang login
    'penduduk'         => 'warga.read',
    'surat'            => 'surat.read',
    'keuangan'         => 'keuangan.read',
    'pengaduan'        => 'pengaduan.read',
    'kegiatan'         => 'kegiatan.read',
    'umkm'             => 'umkm.read',
    'posyandu'         => 'posyandu.read',
    'keamanan'         => 'keamanan.read',
    'statistik'        => 'statistik.read',
    'admin/users'      => 'user.read',
    'admin/roles'      => 'role.read',
    'admin/permissions'=> 'permission.read',
    'admin/user-roles' => 'user.assign_role',
    'admin/audit-log'  => 'log.read',
]);
