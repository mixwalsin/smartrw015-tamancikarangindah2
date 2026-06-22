-- ============================================================
-- Smart RW015 Taman Cikarang Indah 2
-- Migration 002: RBAC Extended – 9 roles + dynamic permissions
-- Jalankan SETELAH schema.sql dan seeder.sql
-- ============================================================

USE `smartrw015`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── Perbarui role yang sudah ada ──────────────────────────────
UPDATE `roles` SET `name` = 'Super Admin',  `slug` = 'super_admin', `description` = 'Akses penuh ke seluruh sistem dan konfigurasi' WHERE `id` = 1;
UPDATE `roles` SET `name` = 'Ketua RW',     `slug` = 'ketua_rw',   `description` = 'Pengurus utama tingkat RW015' WHERE `id` = 2;
UPDATE `roles` SET `name` = 'Ketua RT',     `slug` = 'ketua_rt',   `description` = 'Pengurus utama tingkat RT' WHERE `id` = 3;
UPDATE `roles` SET `name` = 'Warga',        `slug` = 'warga',      `description` = 'Pengguna regular / warga RW015' WHERE `id` = 4;

-- ── Tambah role baru ──────────────────────────────────────────
INSERT IGNORE INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(5, 'Sekretaris RW',   'sekretaris_rw',   'Manajemen administrasi dan surat RW015'),
(6, 'Bendahara RW',    'bendahara_rw',    'Manajemen keuangan dan kas RW015'),
(7, 'Admin RT',        'admin_rt',        'Asisten administratif tingkat RT'),
(8, 'Petugas Posyandu','petugas_posyandu','Mengelola data kesehatan dan posyandu'),
(9, 'Petugas Security','petugas_security','Mengelola keamanan, ronda, dan akses');

-- ── Hapus permissions lama & masukkan ulang (idempotent) ──────
TRUNCATE TABLE `role_permissions`;
TRUNCATE TABLE `permissions`;

-- ── Master Permissions ────────────────────────────────────────
INSERT INTO `permissions` (`id`, `name`, `slug`, `modul`, `description`) VALUES
-- ── Modul: users ──────────────────────────────────────────
( 1, 'Lihat Pengguna',        'user.read',           'users',      'Melihat daftar pengguna'),
( 2, 'Tambah Pengguna',       'user.create',         'users',      'Membuat akun pengguna baru'),
( 3, 'Ubah Pengguna',         'user.update',         'users',      'Mengubah data pengguna'),
( 4, 'Hapus Pengguna',        'user.delete',         'users',      'Menghapus akun pengguna'),
( 5, 'Kelola Role',           'user.assign_role',    'users',      'Menetapkan role ke pengguna'),
-- ── Modul: roles ──────────────────────────────────────────
( 6, 'Lihat Role',            'role.read',           'rbac',       'Melihat daftar role'),
( 7, 'Tambah Role',           'role.create',         'rbac',       'Membuat role baru'),
( 8, 'Ubah Role',             'role.update',         'rbac',       'Mengubah data role'),
( 9, 'Hapus Role',            'role.delete',         'rbac',       'Menghapus role'),
(10, 'Kelola Permission',     'role.assign_permission','rbac',     'Menetapkan permission ke role'),
-- ── Modul: permissions ────────────────────────────────────
(11, 'Lihat Permission',      'permission.read',     'rbac',       'Melihat daftar permission'),
(12, 'Tambah Permission',     'permission.create',   'rbac',       'Membuat permission baru'),
(13, 'Ubah Permission',       'permission.update',   'rbac',       'Mengubah data permission'),
(14, 'Hapus Permission',      'permission.delete',   'rbac',       'Menghapus permission'),
-- ── Modul: warga ──────────────────────────────────────────
(15, 'Lihat Warga',           'warga.read',          'warga',      'Melihat data warga'),
(16, 'Tambah Warga',          'warga.create',        'warga',      'Menambah data warga'),
(17, 'Ubah Warga',            'warga.update',        'warga',      'Mengubah data warga'),
(18, 'Hapus Warga',           'warga.delete',        'warga',      'Menghapus data warga'),
-- ── Modul: surat ──────────────────────────────────────────
(19, 'Lihat Surat',           'surat.read',          'surat',      'Melihat pengajuan surat'),
(20, 'Ajukan Surat',          'surat.create',        'surat',      'Mengajukan surat baru'),
(21, 'Verifikasi Surat RT',   'surat.verify',        'surat',      'Verifikasi surat oleh RT'),
(22, 'Setujui Surat RW',      'surat.approve',       'surat',      'Persetujuan surat oleh RW'),
(23, 'Tolak Surat',           'surat.reject',        'surat',      'Menolak pengajuan surat'),
(24, 'Cetak Surat',           'surat.print',         'surat',      'Mencetak dokumen surat'),
(25, 'Kelola Template Surat', 'surat.manage',        'surat',      'Mengelola template/jenis surat'),
-- ── Modul: keuangan ───────────────────────────────────────
(26, 'Lihat Keuangan',        'keuangan.read',       'keuangan',   'Melihat data keuangan'),
(27, 'Tambah Transaksi',      'keuangan.create',     'keuangan',   'Mencatat transaksi keuangan'),
(28, 'Ubah Transaksi',        'keuangan.update',     'keuangan',   'Mengubah catatan keuangan'),
(29, 'Hapus Transaksi',       'keuangan.delete',     'keuangan',   'Menghapus catatan keuangan'),
(30, 'Ekspor Keuangan',       'keuangan.export',     'keuangan',   'Ekspor laporan keuangan'),
-- ── Modul: pengaduan ──────────────────────────────────────
(31, 'Lihat Pengaduan',       'pengaduan.read',      'pengaduan',  'Melihat daftar pengaduan'),
(32, 'Buat Pengaduan',        'pengaduan.create',    'pengaduan',  'Membuat pengaduan baru'),
(33, 'Kelola Pengaduan',      'pengaduan.manage',    'pengaduan',  'Merespons dan menutup pengaduan'),
-- ── Modul: kegiatan ───────────────────────────────────────
(34, 'Lihat Kegiatan',        'kegiatan.read',       'kegiatan',   'Melihat agenda kegiatan'),
(35, 'Tambah Kegiatan',       'kegiatan.create',     'kegiatan',   'Membuat kegiatan baru'),
(36, 'Ubah Kegiatan',         'kegiatan.update',     'kegiatan',   'Mengubah data kegiatan'),
(37, 'Hapus Kegiatan',        'kegiatan.delete',     'kegiatan',   'Menghapus kegiatan'),
-- ── Modul: inventaris ─────────────────────────────────────
(38, 'Lihat Inventaris',      'inventaris.read',     'inventaris', 'Melihat daftar inventaris'),
(39, 'Kelola Inventaris',     'inventaris.manage',   'inventaris', 'Mengelola inventaris'),
-- ── Modul: umkm ───────────────────────────────────────────
(40, 'Lihat UMKM',            'umkm.read',           'umkm',       'Melihat data UMKM'),
(41, 'Kelola UMKM',           'umkm.manage',         'umkm',       'Mengelola data UMKM'),
-- ── Modul: posyandu ───────────────────────────────────────
(42, 'Lihat Posyandu',        'posyandu.read',       'posyandu',   'Melihat data posyandu'),
(43, 'Input Data Posyandu',   'posyandu.create',     'posyandu',   'Menginput data posyandu'),
(44, 'Kelola Posyandu',       'posyandu.manage',     'posyandu',   'Mengelola seluruh data posyandu'),
-- ── Modul: keamanan ───────────────────────────────────────
(45, 'Lihat Keamanan',        'keamanan.read',       'keamanan',   'Melihat jadwal & log keamanan'),
(46, 'Input Jadwal Keamanan', 'keamanan.create',     'keamanan',   'Mencatat jadwal ronda'),
(47, 'Kelola Keamanan',       'keamanan.manage',     'keamanan',   'Mengelola seluruh data keamanan'),
(48, 'Monitor Keamanan',      'keamanan.monitor',    'keamanan',   'Monitoring keamanan real-time'),
-- ── Modul: pengumuman ─────────────────────────────────────
(49, 'Lihat Pengumuman',      'pengumuman.read',     'pengumuman', 'Melihat pengumuman'),
(50, 'Kelola Pengumuman',     'pengumuman.manage',   'pengumuman', 'Membuat dan mengelola pengumuman'),
-- ── Modul: statistik ──────────────────────────────────────
(51, 'Lihat Statistik',       'statistik.read',      'statistik',  'Melihat laporan statistik'),
(52, 'Ekspor Statistik',      'statistik.export',    'statistik',  'Ekspor data statistik'),
-- ── Modul: log aktivitas ──────────────────────────────────
(53, 'Lihat Log Aktivitas',   'log.read',            'log',        'Melihat audit trail aktivitas');

-- ── role_permissions: Super Admin (1) – semua permission ─────
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions`;

-- ── role_permissions: Ketua RW (2) ────────────────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2,15),(2,16),(2,17),(2,18),
(2,19),(2,21),(2,22),(2,23),(2,24),(2,25),
(2,26),(2,30),
(2,31),(2,33),
(2,34),(2,35),(2,36),(2,37),
(2,38),(2,39),
(2,40),(2,41),
(2,42),(2,44),
(2,45),(2,47),(2,48),
(2,49),(2,50),
(2,51),(2,52),
(2,53);

-- ── role_permissions: Ketua RT (3) ────────────────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3,15),(3,16),(3,17),
(3,19),(3,21),(3,23),(3,24),
(3,26),
(3,31),(3,32),(3,33),
(3,34),(3,35),(3,36),
(3,40),(3,41),
(3,45),(3,46),
(3,49),
(3,51);

-- ── role_permissions: Warga (4) ───────────────────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(4,19),(4,20),
(4,31),(4,32),
(4,34),
(4,40),
(4,42),
(4,49);

-- ── role_permissions: Sekretaris RW (5) ──────────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(5,15),(5,16),(5,17),
(5,19),(5,20),(5,21),(5,22),(5,23),(5,24),(5,25),
(5,31),(5,33),
(5,34),(5,35),(5,36),
(5,49),(5,50),
(5,51);

-- ── role_permissions: Bendahara RW (6) ───────────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(6,15),
(6,19),
(6,26),(6,27),(6,28),(6,29),(6,30),
(6,34),
(6,49),
(6,51),(6,52);

-- ── role_permissions: Admin RT (7) ───────────────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(7,15),(7,16),
(7,19),(7,21),(7,24),
(7,31),(7,32),
(7,34),(7,35),
(7,40),
(7,45),(7,46),
(7,49);

-- ── role_permissions: Petugas Posyandu (8) ───────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(8,15),
(8,42),(8,43),(8,44),
(8,49),
(8,51);

-- ── role_permissions: Petugas Security (9) ───────────────────
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(9,15),
(9,45),(9,46),(9,47),(9,48),
(9,49),
(9,51);

SET FOREIGN_KEY_CHECKS = 1;
