-- ============================================================
-- Smart RW015 Taman Cikarang Indah 2
-- Database Schema - MySQL / MariaDB
-- Kompatibel dengan XAMPP
-- ============================================================

CREATE DATABASE IF NOT EXISTS `smartrw015`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `smartrw015`;

-- ── Users ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`          VARCHAR(100)  NOT NULL,
    `username`      VARCHAR(50)   NOT NULL UNIQUE,
    `email`         VARCHAR(100)  NOT NULL UNIQUE,
    `password`      VARCHAR(255)  NOT NULL,
    `role`          ENUM('admin','rw','rt','warga') NOT NULL DEFAULT 'warga',
    `is_active`     TINYINT(1)    NOT NULL DEFAULT 0,
    `last_login_at` DATETIME      NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin (password: admin123)
INSERT INTO `users` (`name`,`username`,`email`,`password`,`role`,`is_active`) VALUES
('Administrator','admin','admin@smartrw015.id',
 '$2y$12$eImiTXuWVxfM37uY4JANjO.cHHnTMIb.kQGpYn1K8nKtdlk3sC/9u',
 'admin', 1);

-- ── Penduduk ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `penduduk` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nik`           VARCHAR(16)   NOT NULL UNIQUE,
    `no_kk`         VARCHAR(16)   NULL,
    `nama`          VARCHAR(100)  NOT NULL,
    `tempat_lahir`  VARCHAR(60)   NULL,
    `tanggal_lahir` DATE          NULL,
    `jenis_kelamin` ENUM('L','P') NULL,
    `alamat`        TEXT          NULL,
    `rt`            VARCHAR(3)    NULL,
    `rw`            VARCHAR(3)    NOT NULL DEFAULT '015',
    `status_kawin`  ENUM('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') NULL,
    `agama`         ENUM('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu') NULL,
    `pekerjaan`     VARCHAR(80)   NULL,
    `foto`          VARCHAR(255)  NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_nama`  (`nama`),
    INDEX `idx_rt`    (`rt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Surat ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `surat` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `no_surat`      VARCHAR(50)   NULL,
    `jenis_surat`   VARCHAR(100)  NOT NULL,
    `pemohon_id`    INT UNSIGNED  NOT NULL,
    `keperluan`     TEXT          NULL,
    `status`        ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
    `catatan_admin` TEXT          NULL,
    `approved_by`   INT UNSIGNED  NULL,
    `approved_at`   DATETIME      NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`pemohon_id`)  REFERENCES `penduduk`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Keuangan ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `keuangan` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tanggal`     DATE         NOT NULL,
    `jenis`       ENUM('pemasukan','pengeluaran') NOT NULL,
    `kategori`    VARCHAR(80)  NOT NULL,
    `keterangan`  TEXT         NULL,
    `jumlah`      DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `bukti`       VARCHAR(255) NULL,
    `user_id`     INT UNSIGNED NOT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_tanggal` (`tanggal`),
    INDEX `idx_jenis`   (`jenis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Pengaduan ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pengaduan` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`       INT UNSIGNED NOT NULL,
    `judul`         VARCHAR(150) NOT NULL,
    `isi`           TEXT         NOT NULL,
    `foto`          VARCHAR(255) NULL,
    `status`        ENUM('baru','diproses','selesai','ditolak') NOT NULL DEFAULT 'baru',
    `catatan_admin` TEXT         NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Kegiatan ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `kegiatan` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `judul`       VARCHAR(150) NOT NULL,
    `deskripsi`   TEXT         NULL,
    `tanggal`     DATE         NOT NULL,
    `waktu`       TIME         NULL,
    `lokasi`      VARCHAR(150) NULL,
    `foto`        VARCHAR(255) NULL,
    `user_id`     INT UNSIGNED NOT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── UMKM ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `umkm` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_usaha`   VARCHAR(150) NOT NULL,
    `pemilik`      VARCHAR(100) NOT NULL,
    `kategori`     VARCHAR(80)  NULL,
    `deskripsi`    TEXT         NULL,
    `alamat`       TEXT         NULL,
    `rt`           VARCHAR(3)   NULL,
    `no_hp`        VARCHAR(20)  NULL,
    `foto`         VARCHAR(255) NULL,
    `user_id`      INT UNSIGNED NOT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Posyandu ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `posyandu` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `penduduk_id`   INT UNSIGNED NOT NULL,
    `tanggal`       DATE         NOT NULL,
    `berat_badan`   DECIMAL(5,2) NULL COMMENT 'kg',
    `tinggi_badan`  DECIMAL(5,2) NULL COMMENT 'cm',
    `lingkar_kepala`DECIMAL(5,2) NULL COMMENT 'cm',
    `status_gizi`   VARCHAR(50)  NULL,
    `catatan`       TEXT         NULL,
    `user_id`       INT UNSIGNED NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`penduduk_id`) REFERENCES `penduduk`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Keamanan ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `keamanan` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tanggal`    DATE         NOT NULL,
    `petugas`    VARCHAR(100) NOT NULL,
    `rt`         VARCHAR(3)   NULL,
    `shift`      ENUM('pagi','sore','malam') NOT NULL DEFAULT 'malam',
    `catatan`    TEXT         NULL,
    `user_id`    INT UNSIGNED NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Notifikasi ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `notifikasi` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED  NULL,
    `judul`      VARCHAR(150)  NOT NULL,
    `pesan`      TEXT          NOT NULL,
    `tipe`       VARCHAR(30)   NOT NULL DEFAULT 'info',
    `is_read`    TINYINT(1)    NOT NULL DEFAULT 0,
    `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_read` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
