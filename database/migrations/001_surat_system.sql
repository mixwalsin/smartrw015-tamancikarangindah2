-- ============================================================
-- Smart RW015 Taman Cikarang Indah 2
-- Migration 001 - Sistem Surat Online
-- Workflow: Draft → Menunggu RT → Menunggu RW → Disetujui/Ditolak → Selesai
-- ============================================================

USE `smartrw015`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── Hapus tabel lama jika perlu di-reset ───────────────────
DROP TABLE IF EXISTS `surat_history`;
DROP TABLE IF EXISTS `surat_verifikasi`;
DROP TABLE IF EXISTS `surat_approval`;
DROP TABLE IF EXISTS `surat_pengajuan`;
DROP TABLE IF EXISTS `surat_jenis`;

-- ── 1. SURAT_JENIS (Master Jenis Surat) ───────────────────
CREATE TABLE IF NOT EXISTS `surat_jenis` (
    `id`               SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kode`             VARCHAR(20)   NOT NULL UNIQUE,
    `nama`             VARCHAR(150)  NOT NULL,
    `deskripsi`        TEXT          NULL,
    `syarat`           TEXT          NULL COMMENT 'Persyaratan pengajuan, pisahkan dengan newline',
    `template_isi`     LONGTEXT      NULL COMMENT 'Template isi surat dengan placeholder {nama}, {nik}, dsb.',
    `is_active`        TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_surat_jenis_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master jenis / template surat';

-- ── 2. SURAT_PENGAJUAN (Permohonan Surat) ─────────────────
CREATE TABLE IF NOT EXISTS `surat_pengajuan` (
    `id`                  INT UNSIGNED      AUTO_INCREMENT PRIMARY KEY,
    `jenis_id`            SMALLINT UNSIGNED NOT NULL,
    `no_surat`            VARCHAR(80)       NULL    COMMENT 'Nomor surat, diisi setelah disetujui RW',
    `kode_verifikasi`     VARCHAR(64)       NOT NULL UNIQUE COMMENT 'Kode unik untuk QR verification',

    -- Data Pemohon (snapshot, bebas dari FK warga)
    `pemohon_nama`        VARCHAR(100)      NOT NULL,
    `pemohon_nik`         VARCHAR(16)       NOT NULL,
    `pemohon_tempat_lahir` VARCHAR(60)      NULL,
    `pemohon_tgl_lahir`   DATE              NULL,
    `pemohon_jk`          ENUM('L','P')     NULL,
    `pemohon_alamat`      TEXT              NOT NULL,
    `pemohon_rt`          VARCHAR(5)        NOT NULL DEFAULT '001',
    `pemohon_agama`       VARCHAR(30)       NULL,
    `pemohon_pekerjaan`   VARCHAR(80)       NULL,
    `pemohon_no_hp`       VARCHAR(20)       NULL,

    -- Keperluan & Lampiran
    `keperluan`           TEXT              NOT NULL,
    `keterangan_tambahan` TEXT              NULL,
    `lampiran`            VARCHAR(255)      NULL    COMMENT 'Path file lampiran',

    -- Workflow Status
    `status`              ENUM(
                            'draft',
                            'menunggu_rt',
                            'menunggu_rw',
                            'disetujui',
                            'ditolak',
                            'selesai'
                          ) NOT NULL DEFAULT 'menunggu_rt',

    -- Dibuat oleh
    `dibuat_oleh`         INT UNSIGNED      NOT NULL,
    `dibuat_at`           DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- RT Verification
    `rt_verifikasi_oleh`  INT UNSIGNED      NULL,
    `rt_verifikasi_at`    DATETIME          NULL,
    `rt_catatan`          TEXT              NULL,
    `rt_status`           ENUM('menunggu','diverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',

    -- RW Approval
    `rw_approval_oleh`    INT UNSIGNED      NULL,
    `rw_approval_at`      DATETIME          NULL,
    `rw_catatan`          TEXT              NULL,
    `rw_status`           ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',

    -- Timestamps
    `created_at`          DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_sp_jenis`   (`jenis_id`),
    INDEX `idx_sp_dibuat`  (`dibuat_oleh`),
    INDEX `idx_sp_status`  (`status`),
    INDEX `idx_sp_nik`     (`pemohon_nik`),
    INDEX `idx_sp_rt`      (`pemohon_rt`),
    INDEX `idx_sp_kode`    (`kode_verifikasi`),

    CONSTRAINT `fk_sp_jenis`     FOREIGN KEY (`jenis_id`)           REFERENCES `surat_jenis`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_sp_dibuat`    FOREIGN KEY (`dibuat_oleh`)         REFERENCES `users`(`id`)       ON DELETE RESTRICT,
    CONSTRAINT `fk_sp_rt_verif`  FOREIGN KEY (`rt_verifikasi_oleh`) REFERENCES `users`(`id`)       ON DELETE SET NULL,
    CONSTRAINT `fk_sp_rw_appr`   FOREIGN KEY (`rw_approval_oleh`)   REFERENCES `users`(`id`)       ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pengajuan / permohonan surat oleh warga dengan workflow RT-RW';

-- ── 3. SURAT_HISTORY (Audit Trail) ────────────────────────
CREATE TABLE IF NOT EXISTS `surat_history` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pengajuan_id`    INT UNSIGNED NOT NULL,
    `status_lama`     VARCHAR(30)  NOT NULL,
    `status_baru`     VARCHAR(30)  NOT NULL,
    `catatan`         TEXT         NULL,
    `dilakukan_oleh`  INT UNSIGNED NOT NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX `idx_sh_pengajuan` (`pengajuan_id`),
    INDEX `idx_sh_oleh`      (`dilakukan_oleh`),

    CONSTRAINT `fk_sh_pengajuan` FOREIGN KEY (`pengajuan_id`)   REFERENCES `surat_pengajuan`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sh_oleh`      FOREIGN KEY (`dilakukan_oleh`) REFERENCES `users`(`id`)           ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Audit trail perubahan status surat';

SET FOREIGN_KEY_CHECKS = 1;

-- ── Seed Data: 6 Jenis Surat ───────────────────────────────
INSERT INTO `surat_jenis` (`kode`, `nama`, `deskripsi`, `syarat`, `template_isi`, `is_active`) VALUES

('DOMISILI', 'Surat Keterangan Domisili',
 'Surat keterangan bahwa seseorang berdomisili di wilayah RW015 Taman Cikarang Indah 2.',
 'KTP asli\nKartu Keluarga\nSurat pengantar RT',
 'Yang bertanda tangan di bawah ini Ketua RW 015 Taman Cikarang Indah 2, Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi, Provinsi Jawa Barat, menerangkan bahwa:\n\nNama            : {nama}\nNIK             : {nik}\nTempat/Tgl Lahir: {tempat_lahir}, {tgl_lahir}\nJenis Kelamin   : {jenis_kelamin}\nAgama           : {agama}\nPekerjaan       : {pekerjaan}\nAlamat          : {alamat}, RT {rt}/RW015\n\nBenar-benar berdomisili dan beralamat di wilayah RT {rt} RW 015 Taman Cikarang Indah 2.\n\nSurat keterangan ini dibuat untuk keperluan: {keperluan}.',
 1),

('SKCK', 'Surat Pengantar SKCK',
 'Surat pengantar dari RW untuk permohonan Surat Keterangan Catatan Kepolisian (SKCK).',
 'KTP asli\nKartu Keluarga\nFoto 4x6 (2 lembar)\nMaterai 10.000',
 'Yang bertanda tangan di bawah ini Ketua RW 015 Taman Cikarang Indah 2, Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi, Provinsi Jawa Barat, menerangkan bahwa:\n\nNama            : {nama}\nNIK             : {nik}\nTempat/Tgl Lahir: {tempat_lahir}, {tgl_lahir}\nJenis Kelamin   : {jenis_kelamin}\nAgama           : {agama}\nPekerjaan       : {pekerjaan}\nAlamat          : {alamat}, RT {rt}/RW015\n\nBenar-benar warga RT {rt} RW 015 yang berkelakuan baik dan tidak pernah terlibat tindak kriminal selama berdomisili di wilayah kami.\n\nSurat pengantar ini dibuat untuk keperluan permohonan SKCK: {keperluan}.',
 1),

('USAHA', 'Surat Keterangan Usaha',
 'Surat keterangan bahwa seseorang menjalankan usaha di wilayah RW015.',
 'KTP asli\nKartu Keluarga\nFoto lokasi usaha',
 'Yang bertanda tangan di bawah ini Ketua RW 015 Taman Cikarang Indah 2, Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi, Provinsi Jawa Barat, menerangkan bahwa:\n\nNama            : {nama}\nNIK             : {nik}\nTempat/Tgl Lahir: {tempat_lahir}, {tgl_lahir}\nJenis Kelamin   : {jenis_kelamin}\nAgama           : {agama}\nAlamat          : {alamat}, RT {rt}/RW015\n\nBenar-benar menjalankan usaha di wilayah RT {rt} RW 015 Taman Cikarang Indah 2.\n\nKeterangan usaha: {keperluan}\n\nSurat keterangan ini dibuat untuk keperluan yang bersangkutan.',
 1),

('TDK_MAMPU', 'Surat Keterangan Tidak Mampu',
 'Surat keterangan bahwa seseorang termasuk warga kurang mampu secara ekonomi.',
 'KTP asli\nKartu Keluarga\nSurat pengantar RT\nFoto rumah',
 'Yang bertanda tangan di bawah ini Ketua RW 015 Taman Cikarang Indah 2, Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi, Provinsi Jawa Barat, menerangkan bahwa:\n\nNama            : {nama}\nNIK             : {nik}\nTempat/Tgl Lahir: {tempat_lahir}, {tgl_lahir}\nJenis Kelamin   : {jenis_kelamin}\nAgama           : {agama}\nPekerjaan       : {pekerjaan}\nAlamat          : {alamat}, RT {rt}/RW015\n\nBenar-benar warga tidak mampu yang berdomisili di wilayah RT {rt} RW 015 Taman Cikarang Indah 2 dan memerlukan bantuan.\n\nSurat keterangan ini dibuat untuk keperluan: {keperluan}.',
 1),

('KELAHIRAN', 'Surat Keterangan Kelahiran',
 'Surat keterangan kelahiran anak bagi warga RW015.',
 'KTP orang tua\nKartu Keluarga\nSurat keterangan lahir dari bidan/rumah sakit',
 'Yang bertanda tangan di bawah ini Ketua RW 015 Taman Cikarang Indah 2, Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi, Provinsi Jawa Barat, menerangkan bahwa:\n\nNama Orang Tua  : {nama}\nNIK             : {nik}\nAlamat          : {alamat}, RT {rt}/RW015\n\nBenar-benar telah lahir seorang anak di wilayah RT {rt} RW 015 Taman Cikarang Indah 2.\n\nKeterangan kelahiran: {keperluan}\n\nSurat keterangan ini dibuat untuk keperluan pengurusan akta kelahiran.',
 1),

('KEMATIAN', 'Surat Keterangan Kematian',
 'Surat keterangan kematian bagi warga RW015 yang telah meninggal dunia.',
 'KTP almarhum/almarhumah\nKartu Keluarga\nSurat keterangan kematian dari dokter/puskesmas',
 'Yang bertanda tangan di bawah ini Ketua RW 015 Taman Cikarang Indah 2, Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi, Provinsi Jawa Barat, menerangkan bahwa:\n\nNama Pelapor    : {nama}\nNIK             : {nik}\nAlamat          : {alamat}, RT {rt}/RW015\n\nBenar-benar telah meninggal dunia seorang warga yang berdomisili di wilayah RT {rt} RW 015 Taman Cikarang Indah 2.\n\nKeterangan kematian: {keperluan}\n\nSurat keterangan ini dibuat untuk keperluan pengurusan administrasi kematian.',
 1);
