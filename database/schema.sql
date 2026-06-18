-- ============================================================
-- Smart RW015 Taman Cikarang Indah 2
-- Database Schema - MySQL 8.0
-- Versi lengkap dengan 21 modul, Foreign Key & Index
-- ============================================================
--
-- ╔══════════════════════════════════════════════════════════╗
-- ║                  ENTITY RELATIONSHIP DIAGRAM             ║
-- ╠══════════════════════════════════════════════════════════╣
-- ║                                                          ║
-- ║  roles ──< role_permissions >── permissions              ║
-- ║    │                                                     ║
-- ║    └──< users >──< user_permissions >── permissions      ║
-- ║              │                                           ║
-- ║              ├── log_aktivitas                           ║
-- ║              ├── notifikasi                              ║
-- ║              ├── pengumuman                              ║
-- ║              ├── kegiatan ──< kegiatan_peserta           ║
-- ║              ├── kas_rw                                  ║
-- ║              ├── kas_rt                                  ║
-- ║              ├── inventaris                              ║
-- ║              └── [dibuat_oleh / disetujui_oleh on]       ║
-- ║                  pengaduan, pengajuan_surat,             ║
-- ║                  posyandu, security, umkm                ║
-- ║                                                          ║
-- ║  rw (1) ──── (many) rt                                   ║
-- ║               │                                          ║
-- ║               ├──< kk >──< warga >──< keluarga           ║
-- ║               ├──< rumah                                 ║
-- ║               ├──< kas_rt                                ║
-- ║               ├──< kegiatan                             ║
-- ║               ├──< security                              ║
-- ║               ├──< umkm                                  ║
-- ║               └──< pengumuman                            ║
-- ║                                                          ║
-- ║  warga (1) ── (many) keluarga  [dalam KK]                ║
-- ║  warga (1) ── (many) pengajuan_surat                     ║
-- ║  warga (1) ── (many) pengaduan                           ║
-- ║  warga (1) ── (many) posyandu                            ║
-- ║  warga (1) ── (many) umkm                                ║
-- ║  warga (1) ── (many) kegiatan_peserta                    ║
-- ║                                                          ║
-- ║  surat (1) ── (many) pengajuan_surat                     ║
-- ╚══════════════════════════════════════════════════════════╝

-- ============================================================
-- Wilayah RW015 - RT001 s/d RT007
-- ============================================================

CREATE DATABASE IF NOT EXISTS `smartrw015`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `smartrw015`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. ROLES ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(50)  NOT NULL,
    `slug`        VARCHAR(50)  NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master role pengguna';

-- ── 2. PERMISSIONS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `permissions` (
    `id`          SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(100) NOT NULL UNIQUE,
    `modul`       VARCHAR(50)  NOT NULL COMMENT 'Nama modul pemilik permission',
    `description` VARCHAR(255) NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master permission / hak akses';

-- ── 3. ROLE_PERMISSIONS ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id`       TINYINT UNSIGNED  NOT NULL,
    `permission_id` SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role`       FOREIGN KEY (`role_id`)       REFERENCES `roles`(`id`)       ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pivot: role ↔ permission';

-- ── 4. RW ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rw` (
    `id`                  TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kode`                VARCHAR(3)   NOT NULL UNIQUE COMMENT 'Contoh: 015',
    `nama`                VARCHAR(100) NOT NULL,
    `alamat_sekretariat`  TEXT         NULL,
    `kelurahan`           VARCHAR(100) NULL DEFAULT 'Taman Cikarang Indah 2',
    `kecamatan`           VARCHAR(100) NULL DEFAULT 'Cikarang Selatan',
    `kabupaten`           VARCHAR(100) NULL DEFAULT 'Bekasi',
    `provinsi`            VARCHAR(100) NULL DEFAULT 'Jawa Barat',
    `periode_mulai`       YEAR         NULL,
    `periode_selesai`     YEAR         NULL,
    `created_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data RW';

-- ── 5. RT ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rt` (
    `id`              TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rw_id`           TINYINT UNSIGNED NOT NULL,
    `kode`            VARCHAR(3)   NOT NULL COMMENT 'Contoh: 001..007',
    `nama`            VARCHAR(100) NOT NULL,
    `periode_mulai`   YEAR         NULL,
    `periode_selesai` YEAR         NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_rt_rw_kode` (`rw_id`, `kode`),
    CONSTRAINT `fk_rt_rw` FOREIGN KEY (`rw_id`) REFERENCES `rw`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data RT (001–007) dalam RW015';

-- ── 6. KK (Kartu Keluarga) ─────────────────────────────────
CREATE TABLE IF NOT EXISTS `kk` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rt_id`      TINYINT UNSIGNED NOT NULL,
    `no_kk`      VARCHAR(16)  NOT NULL UNIQUE,
    `alamat`     TEXT         NOT NULL,
    `rt_text`    VARCHAR(3)   NULL COMMENT 'Nomor RT tekstual, redundan untuk kemudahan query',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kk_rt` (`rt_id`),
    CONSTRAINT `fk_kk_rt` FOREIGN KEY (`rt_id`) REFERENCES `rt`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kartu Keluarga';

-- ── 7. WARGA ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `warga` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kk_id`          INT UNSIGNED  NOT NULL,
    `nik`            VARCHAR(16)   NOT NULL UNIQUE,
    `nama`           VARCHAR(100)  NOT NULL,
    `tempat_lahir`   VARCHAR(60)   NULL,
    `tanggal_lahir`  DATE          NULL,
    `jenis_kelamin`  ENUM('L','P') NULL,
    `agama`          ENUM('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu') NULL,
    `pendidikan`     ENUM('Tidak Sekolah','SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3') NULL,
    `pekerjaan`      VARCHAR(80)   NULL,
    `status_kawin`   ENUM('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') NULL,
    `status_warga`   ENUM('tetap','pendatang','pindah','meninggal') NOT NULL DEFAULT 'tetap',
    `foto`           VARCHAR(255)  NULL,
    `keterangan`     TEXT          NULL,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_warga_kk`     (`kk_id`),
    INDEX `idx_warga_nama`   (`nama`),
    INDEX `idx_warga_status` (`status_warga`),
    CONSTRAINT `fk_warga_kk` FOREIGN KEY (`kk_id`) REFERENCES `kk`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data individu warga / penduduk';

-- ── 8. KELUARGA (Hubungan anggota dalam KK) ────────────────
CREATE TABLE IF NOT EXISTS `keluarga` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kk_id`      INT UNSIGNED NOT NULL,
    `warga_id`   INT UNSIGNED NOT NULL,
    `hubungan`   ENUM(
                   'Kepala Keluarga',
                   'Istri',
                   'Anak',
                   'Menantu',
                   'Cucu',
                   'Orang Tua',
                   'Mertua',
                   'Famili Lain',
                   'Pembantu',
                   'Lainnya'
                 ) NOT NULL DEFAULT 'Lainnya',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_keluarga_warga` (`warga_id`),
    INDEX `idx_keluarga_kk` (`kk_id`),
    CONSTRAINT `fk_keluarga_kk`    FOREIGN KEY (`kk_id`)    REFERENCES `kk`(`id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_keluarga_warga` FOREIGN KEY (`warga_id`) REFERENCES `warga`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Hubungan anggota keluarga dalam satu KK';

-- ── 9. RUMAH ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rumah` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rt_id`          TINYINT UNSIGNED NOT NULL,
    `kk_id`          INT UNSIGNED     NULL COMMENT 'NULL = rumah kosong / tidak berpenghuni',
    `no_urut`        VARCHAR(10)      NOT NULL,
    `alamat`         TEXT             NOT NULL,
    `status_hunian`  ENUM('milik','sewa','kontrak','kosong','lainnya') NOT NULL DEFAULT 'milik',
    `koordinat_lat`  DECIMAL(10,8)    NULL,
    `koordinat_lng`  DECIMAL(11,8)    NULL,
    `keterangan`     TEXT             NULL,
    `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_rumah_rt`  (`rt_id`),
    INDEX `idx_rumah_kk`  (`kk_id`),
    CONSTRAINT `fk_rumah_rt` FOREIGN KEY (`rt_id`) REFERENCES `rt`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_rumah_kk` FOREIGN KEY (`kk_id`) REFERENCES `kk`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data rumah / hunian per RT';

-- ── 10. USERS ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    `role_id`       TINYINT UNSIGNED NOT NULL,
    `warga_id`      INT UNSIGNED     NULL COMMENT 'NULL untuk admin non-warga',
    `name`          VARCHAR(100)     NOT NULL,
    `username`      VARCHAR(50)      NOT NULL UNIQUE,
    `email`         VARCHAR(100)     NOT NULL UNIQUE,
    `password`      VARCHAR(255)     NOT NULL,
    `avatar`        VARCHAR(255)     NULL,
    `is_active`     TINYINT(1)       NOT NULL DEFAULT 0,
    `last_login_at` DATETIME         NULL,
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role`   (`role_id`),
    INDEX `idx_users_warga`  (`warga_id`),
    CONSTRAINT `fk_users_role`  FOREIGN KEY (`role_id`)  REFERENCES `roles`(`id`)  ON DELETE RESTRICT,
    CONSTRAINT `fk_users_warga` FOREIGN KEY (`warga_id`) REFERENCES `warga`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Akun pengguna sistem';

-- ── 11. USER_PERMISSIONS (direct override) ─────────────────
CREATE TABLE IF NOT EXISTS `user_permissions` (
    `user_id`       INT UNSIGNED      NOT NULL,
    `permission_id` SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `permission_id`),
    CONSTRAINT `fk_up_user`       FOREIGN KEY (`user_id`)       REFERENCES `users`(`id`)       ON DELETE CASCADE,
    CONSTRAINT `fk_up_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Permission tambahan langsung ke user (override role)';

-- ── 12. SURAT (Template / Jenis Surat) ─────────────────────
CREATE TABLE IF NOT EXISTS `surat` (
    `id`               SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kode`             VARCHAR(20)   NOT NULL UNIQUE,
    `nama`             VARCHAR(150)  NOT NULL,
    `template_content` LONGTEXT      NULL COMMENT 'Template HTML/teks surat dengan placeholder {nama}, {nik}, dsb.',
    `syarat`           TEXT          NULL COMMENT 'Persyaratan pengajuan',
    `is_active`        TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master jenis / template surat';

-- ── 13. PENGAJUAN_SURAT ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pengajuan_surat` (
    `id`              INT UNSIGNED      AUTO_INCREMENT PRIMARY KEY,
    `surat_id`        SMALLINT UNSIGNED NOT NULL,
    `warga_id`        INT UNSIGNED      NOT NULL,
    `no_surat`        VARCHAR(60)       NULL COMMENT 'Diisi setelah disetujui',
    `keperluan`       TEXT              NOT NULL,
    `lampiran`        VARCHAR(255)      NULL COMMENT 'Path file lampiran',
    `status`          ENUM('draft','pending','diproses','selesai','ditolak') NOT NULL DEFAULT 'pending',
    `catatan`         TEXT              NULL,
    `disetujui_oleh`  INT UNSIGNED      NULL,
    `disetujui_at`    DATETIME          NULL,
    `created_at`      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ps_warga`   (`warga_id`),
    INDEX `idx_ps_surat`   (`surat_id`),
    INDEX `idx_ps_status`  (`status`),
    CONSTRAINT `fk_ps_surat`        FOREIGN KEY (`surat_id`)       REFERENCES `surat`(`id`)  ON DELETE RESTRICT,
    CONSTRAINT `fk_ps_warga`        FOREIGN KEY (`warga_id`)       REFERENCES `warga`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ps_disetujui`    FOREIGN KEY (`disetujui_oleh`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pengajuan / permohonan surat oleh warga';

-- ── 14. PENGADUAN ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pengaduan` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `warga_id`        INT UNSIGNED NOT NULL,
    `kategori`        VARCHAR(80)  NOT NULL DEFAULT 'Umum'
                                   COMMENT 'Contoh: Infrastruktur, Keamanan, Kebersihan, Sosial, Lainnya',
    `judul`           VARCHAR(150) NOT NULL,
    `isi`             TEXT         NOT NULL,
    `foto`            VARCHAR(255) NULL,
    `status`          ENUM('baru','diproses','selesai','ditolak') NOT NULL DEFAULT 'baru',
    `catatan_admin`   TEXT         NULL,
    `ditangani_oleh`  INT UNSIGNED NULL,
    `ditangani_at`    DATETIME     NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pengaduan_warga`    (`warga_id`),
    INDEX `idx_pengaduan_status`   (`status`),
    INDEX `idx_pengaduan_kategori` (`kategori`(20)),
    CONSTRAINT `fk_pengaduan_warga`    FOREIGN KEY (`warga_id`)       REFERENCES `warga`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pengaduan_petugas`  FOREIGN KEY (`ditangani_oleh`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pengaduan / laporan masalah dari warga';

-- ── 15. KEGIATAN ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `kegiatan` (
    `id`           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    `rt_id`        TINYINT UNSIGNED NULL COMMENT 'NULL = kegiatan seluruh RW',
    `judul`        VARCHAR(150)     NOT NULL,
    `deskripsi`    TEXT             NULL,
    `tanggal`      DATE             NOT NULL,
    `waktu_mulai`  TIME             NULL,
    `waktu_selesai`TIME             NULL,
    `lokasi`       VARCHAR(150)     NULL,
    `foto`         VARCHAR(255)     NULL,
    `is_published` TINYINT(1)       NOT NULL DEFAULT 0,
    `dibuat_oleh`  INT UNSIGNED     NOT NULL,
    `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kegiatan_tanggal` (`tanggal`),
    INDEX `idx_kegiatan_rt`      (`rt_id`),
    CONSTRAINT `fk_kegiatan_rt`   FOREIGN KEY (`rt_id`)       REFERENCES `rt`(`id`)    ON DELETE SET NULL,
    CONSTRAINT `fk_kegiatan_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Agenda / kegiatan warga RW atau RT';

-- ── 15a. KEGIATAN_PESERTA ──────────────────────────────────
CREATE TABLE IF NOT EXISTS `kegiatan_peserta` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kegiatan_id` INT UNSIGNED NOT NULL,
    `warga_id`    INT UNSIGNED NOT NULL,
    `hadir`       TINYINT(1)   NOT NULL DEFAULT 0,
    `catatan`     VARCHAR(255) NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_kp_kegiatan_warga` (`kegiatan_id`, `warga_id`),
    CONSTRAINT `fk_kp_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_kp_warga`    FOREIGN KEY (`warga_id`)    REFERENCES `warga`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Daftar hadir peserta kegiatan';

-- ── 16. KAS_RW ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `kas_rw` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tanggal`       DATE          NOT NULL,
    `jenis`         ENUM('pemasukan','pengeluaran') NOT NULL,
    `kategori`      VARCHAR(80)   NOT NULL COMMENT 'Contoh: Iuran, Donasi, Operasional, ATK, dll.',
    `keterangan`    TEXT          NULL,
    `jumlah`        DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `saldo_setelah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `bukti`         VARCHAR(255)  NULL COMMENT 'Path file bukti transaksi',
    `dibuat_oleh`   INT UNSIGNED  NOT NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kasrw_tanggal` (`tanggal`),
    INDEX `idx_kasrw_jenis`   (`jenis`),
    CONSTRAINT `fk_kasrw_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Buku kas RW015';

-- ── 17. KAS_RT ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `kas_rt` (
    `id`            INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    `rt_id`         TINYINT UNSIGNED NOT NULL,
    `tanggal`       DATE             NOT NULL,
    `jenis`         ENUM('pemasukan','pengeluaran') NOT NULL,
    `kategori`      VARCHAR(80)      NOT NULL,
    `keterangan`    TEXT             NULL,
    `jumlah`        DECIMAL(15,2)    NOT NULL DEFAULT 0.00,
    `saldo_setelah` DECIMAL(15,2)    NOT NULL DEFAULT 0.00,
    `bukti`         VARCHAR(255)     NULL,
    `dibuat_oleh`   INT UNSIGNED     NOT NULL,
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kasrt_rt`      (`rt_id`),
    INDEX `idx_kasrt_tanggal` (`tanggal`),
    INDEX `idx_kasrt_jenis`   (`jenis`),
    CONSTRAINT `fk_kasrt_rt`   FOREIGN KEY (`rt_id`)       REFERENCES `rt`(`id`)    ON DELETE RESTRICT,
    CONSTRAINT `fk_kasrt_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Buku kas per RT';

-- ── 18. INVENTARIS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `inventaris` (
    `id`                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kode_barang`        VARCHAR(30)  NOT NULL UNIQUE,
    `nama`               VARCHAR(150) NOT NULL,
    `kategori`           VARCHAR(80)  NULL COMMENT 'Contoh: Elektronik, Furnitur, Alat Kebersihan, dll.',
    `kondisi`            ENUM('baik','rusak_ringan','rusak_berat','tidak_layak') NOT NULL DEFAULT 'baik',
    `jumlah`             INT UNSIGNED NOT NULL DEFAULT 1,
    `satuan`             VARCHAR(20)  NOT NULL DEFAULT 'unit',
    `lokasi`             VARCHAR(100) NULL COMMENT 'Contoh: Pos Satpam RT001, Balai RW',
    `tanggal_pengadaan`  DATE         NULL,
    `nilai_perolehan`    DECIMAL(15,2) NULL,
    `foto`               VARCHAR(255) NULL,
    `keterangan`         TEXT         NULL,
    `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_inventaris_kondisi`  (`kondisi`),
    INDEX `idx_inventaris_kategori` (`kategori`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Inventaris barang milik RW015';

-- ── 19. UMKM ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `umkm` (
    `id`          INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    `warga_id`    INT UNSIGNED     NULL COMMENT 'Pemilik UMKM (warga terdaftar)',
    `rt_id`       TINYINT UNSIGNED NULL,
    `nama_usaha`  VARCHAR(150)     NOT NULL,
    `kategori`    VARCHAR(80)      NULL COMMENT 'Kuliner, Fashion, Jasa, Kerajinan, dll.',
    `deskripsi`   TEXT             NULL,
    `produk`      TEXT             NULL COMMENT 'Daftar produk / layanan',
    `alamat`      TEXT             NULL,
    `no_hp`       VARCHAR(20)      NULL,
    `email`       VARCHAR(100)     NULL,
    `foto`        VARCHAR(255)     NULL,
    `status`      ENUM('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
    `dibuat_oleh` INT UNSIGNED     NOT NULL,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_umkm_warga`    (`warga_id`),
    INDEX `idx_umkm_rt`       (`rt_id`),
    INDEX `idx_umkm_status`   (`status`),
    INDEX `idx_umkm_kategori` (`kategori`(20)),
    CONSTRAINT `fk_umkm_warga`  FOREIGN KEY (`warga_id`)    REFERENCES `warga`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_umkm_rt`     FOREIGN KEY (`rt_id`)       REFERENCES `rt`(`id`)    ON DELETE SET NULL,
    CONSTRAINT `fk_umkm_user`   FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data UMKM warga RW015';

-- ── 20. POSYANDU ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `posyandu` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `warga_id`        INT UNSIGNED  NOT NULL,
    `tanggal`         DATE          NOT NULL,
    `jenis_kegiatan`  ENUM('balita','lansia','ibu_hamil','remaja','umum') NOT NULL DEFAULT 'balita',
    `berat_badan`     DECIMAL(5,2)  NULL COMMENT 'kg',
    `tinggi_badan`    DECIMAL(5,2)  NULL COMMENT 'cm',
    `lingkar_kepala`  DECIMAL(5,2)  NULL COMMENT 'cm',
    `lingkar_lengan`  DECIMAL(5,2)  NULL COMMENT 'cm',
    `tekanan_darah`   VARCHAR(15)   NULL COMMENT 'Format: 120/80',
    `status_gizi`     VARCHAR(50)   NULL COMMENT 'Gizi Baik, Gizi Kurang, Stunting, dsb.',
    `imunisasi`       VARCHAR(100)  NULL,
    `catatan`         TEXT          NULL,
    `petugas_id`      INT UNSIGNED  NOT NULL,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_posyandu_warga`   (`warga_id`),
    INDEX `idx_posyandu_tanggal` (`tanggal`),
    INDEX `idx_posyandu_jenis`   (`jenis_kegiatan`),
    CONSTRAINT `fk_posyandu_warga`   FOREIGN KEY (`warga_id`)  REFERENCES `warga`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_posyandu_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Rekam data posyandu (balita, lansia, ibu hamil)';

-- ── 21. SECURITY (Jadwal & Log Keamanan) ───────────────────
CREATE TABLE IF NOT EXISTS `security` (
    `id`           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    `rt_id`        TINYINT UNSIGNED NULL COMMENT 'NULL = seluruh lingkungan RW',
    `tanggal`      DATE             NOT NULL,
    `petugas`      VARCHAR(100)     NOT NULL,
    `shift`        ENUM('pagi','siang','malam') NOT NULL DEFAULT 'malam',
    `jam_mulai`    TIME             NULL,
    `jam_selesai`  TIME             NULL,
    `catatan`      TEXT             NULL,
    `status`       ENUM('terjadwal','aktif','selesai') NOT NULL DEFAULT 'terjadwal',
    `dibuat_oleh`  INT UNSIGNED     NOT NULL,
    `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_security_tanggal` (`tanggal`),
    INDEX `idx_security_rt`      (`rt_id`),
    INDEX `idx_security_shift`   (`shift`),
    CONSTRAINT `fk_security_rt`   FOREIGN KEY (`rt_id`)       REFERENCES `rt`(`id`)    ON DELETE SET NULL,
    CONSTRAINT `fk_security_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Jadwal dan log keamanan / ronda';

-- ── 22. PENGUMUMAN ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pengumuman` (
    `id`           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    `rt_id`        TINYINT UNSIGNED NULL COMMENT 'NULL = pengumuman untuk seluruh RW',
    `judul`        VARCHAR(150)     NOT NULL,
    `isi`          LONGTEXT         NOT NULL,
    `foto`         VARCHAR(255)     NULL,
    `jenis`        ENUM('umum','penting','darurat') NOT NULL DEFAULT 'umum',
    `is_published` TINYINT(1)       NOT NULL DEFAULT 0,
    `published_at` DATETIME         NULL,
    `expired_at`   DATETIME         NULL,
    `dibuat_oleh`  INT UNSIGNED     NOT NULL,
    `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pengumuman_rt`        (`rt_id`),
    INDEX `idx_pengumuman_jenis`     (`jenis`),
    INDEX `idx_pengumuman_published` (`is_published`, `published_at`),
    CONSTRAINT `fk_pengumuman_rt`   FOREIGN KEY (`rt_id`)       REFERENCES `rt`(`id`)    ON DELETE SET NULL,
    CONSTRAINT `fk_pengumuman_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pengumuman / informasi untuk warga';

-- ── 23. NOTIFIKASI ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `notifikasi` (
    `id`         INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED  NULL COMMENT 'NULL = broadcast ke semua user',
    `judul`      VARCHAR(150)  NOT NULL,
    `pesan`      TEXT          NOT NULL,
    `tipe`       ENUM('info','sukses','peringatan','error') NOT NULL DEFAULT 'info',
    `url`        VARCHAR(255)  NULL COMMENT 'Link tujuan saat notifikasi diklik',
    `is_read`    TINYINT(1)    NOT NULL DEFAULT 0,
    `read_at`    DATETIME      NULL,
    `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_user_read` (`user_id`, `is_read`),
    INDEX `idx_notif_created`   (`created_at`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Notifikasi sistem untuk pengguna';

-- ── 24. LOG_AKTIVITAS ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `log_aktivitas` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED  NULL,
    `aksi`        VARCHAR(50)   NOT NULL COMMENT 'Contoh: login, create, update, delete, approve',
    `modul`       VARCHAR(50)   NOT NULL COMMENT 'Nama modul/tabel yang terdampak',
    `data_id`     INT UNSIGNED  NULL COMMENT 'ID record yang terdampak',
    `keterangan`  TEXT          NULL,
    `ip_address`  VARCHAR(45)   NULL COMMENT 'IPv4 atau IPv6',
    `user_agent`  VARCHAR(255)  NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_log_user`    (`user_id`),
    INDEX `idx_log_modul`   (`modul`),
    INDEX `idx_log_aksi`    (`aksi`),
    INDEX `idx_log_created` (`created_at`),
    CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Audit trail aktivitas seluruh pengguna';

SET FOREIGN_KEY_CHECKS = 1;

-- ── 25. PENDUDUK (Modul Administrasi Penduduk) ─────────────
-- Tabel utama untuk modul administrasi penduduk
CREATE TABLE IF NOT EXISTS `penduduk` (
    `id`             INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    `nik`            VARCHAR(16)     NOT NULL COMMENT 'Nomor Induk Kependudukan',
    `no_kk`          VARCHAR(16)     NULL     COMMENT 'Nomor Kartu Keluarga',
    `nama`           VARCHAR(100)    NOT NULL,
    `tempat_lahir`   VARCHAR(60)     NULL,
    `tanggal_lahir`  DATE            NULL,
    `jenis_kelamin`  ENUM('L','P')   NULL,
    `agama`          ENUM('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu') NULL,
    `status_kawin`   ENUM('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') NULL,
    `pendidikan`     ENUM('Tidak Sekolah','SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3') NULL,
    `pekerjaan`      VARCHAR(80)     NULL,
    `alamat`         TEXT            NULL,
    `rt`             VARCHAR(3)      NULL     COMMENT 'Nomor RT 001–015',
    `rw`             VARCHAR(3)      NOT NULL DEFAULT '015' COMMENT 'RW015',
    `no_rumah`       VARCHAR(20)     NULL,
    `no_hp`          VARCHAR(20)     NULL,
    `email`          VARCHAR(100)    NULL,
    `status_tinggal` ENUM('Tetap','Pendatang','Pindah','Meninggal') NOT NULL DEFAULT 'Tetap',
    `foto`           VARCHAR(255)    NULL,
    `created_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_penduduk_nik`    (`nik`),
    INDEX  `idx_penduduk_no_kk`    (`no_kk`),
    INDEX  `idx_penduduk_rt`       (`rt`),
    INDEX  `idx_penduduk_rw`       (`rw`),
    INDEX  `idx_penduduk_nama`     (`nama`),
    INDEX  `idx_penduduk_jk`       (`jenis_kelamin`),
    INDEX  `idx_penduduk_status`   (`status_tinggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data administrasi penduduk RW015';
