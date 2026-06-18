-- ============================================================
-- Smart RW015 Taman Cikarang Indah 2
-- Posyandu Module Migration
-- ============================================================

USE `smartrw015`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. BALITA ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `balita` (
    `id`               INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    `nama`             VARCHAR(100)     NOT NULL,
    `jenis_kelamin`    ENUM('L','P')    NOT NULL,
    `tgl_lahir`        DATE             NOT NULL,
    `nama_ibu`         VARCHAR(100)     NOT NULL,
    `nama_ayah`        VARCHAR(100)     NULL,
    `alamat`           TEXT             NULL,
    `no_rumah`         VARCHAR(10)      NULL,
    `rt`               VARCHAR(5)       NOT NULL DEFAULT '007',
    `rw`               VARCHAR(5)       NOT NULL DEFAULT '015',
    `berat_badan`      DECIMAL(5,2)     NULL COMMENT 'kg',
    `tinggi_badan`     DECIMAL(5,2)     NULL COMMENT 'cm',
    `status_imunisasi` ENUM('lengkap','tidak_lengkap','belum') NOT NULL DEFAULT 'belum',
    `catatan`          TEXT             NULL,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_balita_rt` (`rt`),
    INDEX `idx_balita_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data balita usia 0-5 tahun';

-- ── 2. IBU_HAMIL ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `ibu_hamil` (
    `id`                  INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    `nama`                VARCHAR(100)  NOT NULL,
    `umur`                TINYINT UNSIGNED NOT NULL,
    `alamat`              TEXT          NULL,
    `no_rumah`            VARCHAR(10)   NULL,
    `rt`                  VARCHAR(5)    NOT NULL DEFAULT '007',
    `rw`                  VARCHAR(5)    NOT NULL DEFAULT '015',
    `tgl_perkiraan_lahir` DATE          NULL,
    `bulan_kehamilan`     TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `status_kesehatan`    ENUM('normal','berisiko_tinggi') NOT NULL DEFAULT 'normal',
    `catatan`             TEXT          NULL,
    `created_at`          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ibu_hamil_rt` (`rt`),
    INDEX `idx_ibu_hamil_status` (`status_kesehatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data ibu hamil';

-- ── 3. JADWAL_POSYANDU ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `jadwal_posyandu` (
    `id`          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    `tanggal`     DATE          NOT NULL,
    `jam_mulai`   TIME          NOT NULL,
    `jam_selesai` TIME          NOT NULL,
    `lokasi`      VARCHAR(200)  NOT NULL,
    `keterangan`  TEXT          NULL,
    `status`      ENUM('dijadwalkan','berlangsung','selesai','dibatalkan') NOT NULL DEFAULT 'dijadwalkan',
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_jadwal_tanggal` (`tanggal`),
    INDEX `idx_jadwal_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Jadwal pelaksanaan posyandu';

-- ── 4. IMUNISASI ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `imunisasi` (
    `id`                 INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    `balita_id`          INT UNSIGNED  NOT NULL,
    `jenis_imunisasi`    VARCHAR(100)  NOT NULL,
    `tanggal_imunisasi`  DATE          NOT NULL,
    `tempat_imunisasi`   VARCHAR(150)  NULL,
    `catatan`            TEXT          NULL,
    `created_at`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_imunisasi_balita` (`balita_id`),
    CONSTRAINT `fk_imunisasi_balita` FOREIGN KEY (`balita_id`)
        REFERENCES `balita`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data imunisasi balita';

-- ── 5. TIMBANGAN ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `timbangan` (
    `id`              INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    `balita_id`       INT UNSIGNED   NOT NULL,
    `tanggal_timbang` DATE           NOT NULL,
    `berat_badan`     DECIMAL(5,2)   NOT NULL COMMENT 'kg',
    `tinggi_badan`    DECIMAL(5,2)   NULL COMMENT 'cm',
    `status_gizi`     ENUM('gizi_baik','gizi_kurang','gizi_buruk','lebih') NOT NULL DEFAULT 'gizi_baik',
    `catatan`         TEXT           NULL,
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_timbangan_balita` (`balita_id`),
    INDEX `idx_timbangan_tanggal` (`tanggal_timbang`),
    CONSTRAINT `fk_timbangan_balita` FOREIGN KEY (`balita_id`)
        REFERENCES `balita`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data penimbangan balita';

-- ── 6. GRAFIK_PERTUMBUHAN ───────────────────────────────────
CREATE TABLE IF NOT EXISTS `grafik_pertumbuhan` (
    `id`              INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    `balita_id`       INT UNSIGNED   NOT NULL,
    `usia_bulan`      TINYINT UNSIGNED NOT NULL,
    `berat_badan`     DECIMAL(5,2)   NULL COMMENT 'kg',
    `tinggi_badan`    DECIMAL(5,2)   NULL COMMENT 'cm',
    `lingkar_kepala`  DECIMAL(5,2)   NULL COMMENT 'cm',
    `catatan`         TEXT           NULL,
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_grafik_balita` (`balita_id`),
    CONSTRAINT `fk_grafik_balita` FOREIGN KEY (`balita_id`)
        REFERENCES `balita`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data grafik pertumbuhan balita';

SET FOREIGN_KEY_CHECKS = 1;
