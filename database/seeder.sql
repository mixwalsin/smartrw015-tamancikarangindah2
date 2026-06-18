-- ============================================================
-- Smart RW015 Taman Cikarang Indah 2
-- Seed Data — urutan insert mengikuti dependency FK
-- ============================================================

USE `smartrw015`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── roles ──────────────────────────────────────────────────
INSERT INTO `roles` (`id`,`name`,`slug`,`description`) VALUES
(1, 'Administrator', 'admin',  'Akses penuh ke seluruh sistem'),
(2, 'Pengurus RW',   'rw',    'Ketua / sekretaris / bendahara RW015'),
(3, 'Pengurus RT',   'rt',    'Ketua / pengurus RT 001–007'),
(4, 'Warga',         'warga', 'Warga umum RW015');

-- ── permissions ────────────────────────────────────────────
INSERT INTO `permissions` (`id`,`name`,`slug`,`modul`) VALUES
-- warga
( 1,'Lihat Warga',           'warga.view',   'warga'),
( 2,'Tambah Warga',          'warga.create', 'warga'),
( 3,'Ubah Warga',            'warga.edit',   'warga'),
( 4,'Hapus Warga',           'warga.delete', 'warga'),
-- surat
( 5,'Ajukan Surat',          'surat.apply',  'surat'),
( 6,'Kelola Surat',          'surat.manage', 'surat'),
-- pengaduan
( 7,'Buat Pengaduan',        'pengaduan.create','pengaduan'),
( 8,'Kelola Pengaduan',      'pengaduan.manage','pengaduan'),
-- kas
( 9,'Lihat Kas RW',          'kas_rw.view',  'kas_rw'),
(10,'Kelola Kas RW',         'kas_rw.manage','kas_rw'),
(11,'Lihat Kas RT',          'kas_rt.view',  'kas_rt'),
(12,'Kelola Kas RT',         'kas_rt.manage','kas_rt'),
-- kegiatan
(13,'Lihat Kegiatan',        'kegiatan.view',   'kegiatan'),
(14,'Kelola Kegiatan',       'kegiatan.manage', 'kegiatan'),
-- inventaris
(15,'Lihat Inventaris',      'inventaris.view',  'inventaris'),
(16,'Kelola Inventaris',     'inventaris.manage','inventaris'),
-- umkm
(17,'Lihat UMKM',            'umkm.view',    'umkm'),
(18,'Kelola UMKM',           'umkm.manage',  'umkm'),
-- posyandu
(19,'Lihat Posyandu',        'posyandu.view',   'posyandu'),
(20,'Kelola Posyandu',       'posyandu.manage', 'posyandu'),
-- security
(21,'Lihat Keamanan',        'security.view',   'security'),
(22,'Kelola Keamanan',       'security.manage', 'security'),
-- pengumuman
(23,'Lihat Pengumuman',      'pengumuman.view',   'pengumuman'),
(24,'Kelola Pengumuman',     'pengumuman.manage', 'pengumuman'),
-- users
(25,'Kelola Pengguna',       'users.manage', 'users'),
-- log
(26,'Lihat Log Aktivitas',   'log.view',     'log_aktivitas');

-- ── role_permissions ───────────────────────────────────────
-- admin (1): semua permission
INSERT INTO `role_permissions` (`role_id`,`permission_id`) VALUES
(1, 1),(1, 2),(1, 3),(1, 4),(1, 5),(1, 6),(1, 7),(1, 8),
(1, 9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),
(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26);

-- rw (2): hampir semua kecuali user management
INSERT INTO `role_permissions` (`role_id`,`permission_id`) VALUES
(2, 1),(2, 2),(2, 3),(2, 5),(2, 6),(2, 7),(2, 8),
(2, 9),(2,10),(2,11),(2,13),(2,14),(2,15),(2,16),
(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24);

-- rt (3): operasional RT
INSERT INTO `role_permissions` (`role_id`,`permission_id`) VALUES
(3, 1),(3, 5),(3, 6),(3, 7),(3, 8),(3,11),(3,12),
(3,13),(3,14),(3,17),(3,21),(3,23);

-- warga (4): aksi mandiri
INSERT INTO `role_permissions` (`role_id`,`permission_id`) VALUES
(4, 5),(4, 7),(4,13),(4,17),(4,23);

-- ── rw ─────────────────────────────────────────────────────
INSERT INTO `rw` (`id`,`kode`,`nama`,`alamat_sekretariat`,`periode_mulai`,`periode_selesai`) VALUES
(1,'015','RW 015 Taman Cikarang Indah 2',
 'Jl. Taman Cikarang Indah 2, Kelurahan Taman Cikarang Indah, Kec. Cikarang Selatan, Bekasi',
 2024, 2027);

-- ── rt ─────────────────────────────────────────────────────
INSERT INTO `rt` (`id`,`rw_id`,`kode`,`nama`,`periode_mulai`,`periode_selesai`) VALUES
(1,1,'001','RT 001',2024,2027),
(2,1,'002','RT 002',2024,2027),
(3,1,'003','RT 003',2024,2027),
(4,1,'004','RT 004',2024,2027),
(5,1,'005','RT 005',2024,2027),
(6,1,'006','RT 006',2024,2027),
(7,1,'007','RT 007',2024,2027);

-- ── kk ─────────────────────────────────────────────────────
INSERT INTO `kk` (`id`,`rt_id`,`no_kk`,`alamat`,`rt_text`) VALUES
(1,1,'3216051001010001','Jl. Mawar No. 1, RW015','001'),
(2,2,'3216051002010001','Jl. Melati No. 5, RW015','002'),
(3,3,'3216051003010001','Jl. Anggrek No. 3, RW015','003'),
(4,4,'3216051004010001','Jl. Kenanga No. 7, RW015','004'),
(5,5,'3216051005010001','Jl. Dahlia No. 2, RW015','005'),
(6,6,'3216051006010001','Jl. Flamboyan No. 9, RW015','006'),
(7,7,'3216051007010001','Jl. Bougenville No. 4, RW015','007');

-- ── warga ──────────────────────────────────────────────────
INSERT INTO `warga`
(`id`,`kk_id`,`nik`,`nama`,`tempat_lahir`,`tanggal_lahir`,`jenis_kelamin`,`agama`,`pendidikan`,`pekerjaan`,`status_kawin`,`status_warga`) VALUES
( 1,1,'3216051001010001','Budi Santoso',       'Cikarang','1985-01-15','L','Islam','S1','Karyawan Swasta','Kawin',      'tetap'),
( 2,1,'3216051001010002','Siti Aminah',         'Bekasi',  '1990-03-20','P','Islam','S1','Ibu Rumah Tangga','Kawin',    'tetap'),
( 3,1,'3216051001010003','Dika Santoso',        'Bekasi',  '2015-06-10','L','Islam','SD',NULL,            'Belum Kawin','tetap'),
( 4,2,'3216051002010001','Ahmad Fauzi',         'Jakarta', '1978-07-10','L','Islam','S1','Wirausaha',      'Kawin',      'tetap'),
( 5,2,'3216051002010002','Rahmi Fauzi',         'Bandung', '1982-09-05','P','Islam','SMA/SMK','Ibu Rumah Tangga','Kawin','tetap'),
( 6,3,'3216051003010001','Dewi Rahayu',         'Bandung', '1995-11-25','P','Islam','S1','Mahasiswa',      'Belum Kawin','tetap'),
( 7,4,'3216051004010001','Hendra Gunawan',      'Cikarang','1982-04-05','L','Kristen','S2','PNS',          'Kawin',      'tetap'),
( 8,4,'3216051004010002','Maria Gunawan',       'Jakarta', '1985-07-12','P','Kristen','S1','Guru',         'Kawin',      'tetap'),
( 9,5,'3216051005010001','Rudi Hermawan',       'Bekasi',  '1975-02-28','L','Islam','SMA/SMK','Satpam',    'Kawin',      'tetap'),
(10,6,'3216051006010001','Lia Kusuma',          'Surabaya','1993-08-17','P','Islam','D3','Perawat',        'Belum Kawin','tetap'),
(11,7,'3216051007010001','Teguh Prasetyo',      'Yogyakarta','1980-12-03','L','Islam','S1','Pedagang',     'Kawin',      'tetap'),
(12,7,'3216051007010002','Yuni Prasetyo',       'Semarang','1983-05-22','P','Islam','SMA/SMK','Ibu Rumah Tangga','Kawin','tetap');

-- ── keluarga ───────────────────────────────────────────────
INSERT INTO `keluarga` (`kk_id`,`warga_id`,`hubungan`) VALUES
(1, 1,'Kepala Keluarga'),
(1, 2,'Istri'),
(1, 3,'Anak'),
(2, 4,'Kepala Keluarga'),
(2, 5,'Istri'),
(3, 6,'Kepala Keluarga'),
(4, 7,'Kepala Keluarga'),
(4, 8,'Istri'),
(5, 9,'Kepala Keluarga'),
(6,10,'Kepala Keluarga'),
(7,11,'Kepala Keluarga'),
(7,12,'Istri');

-- ── rumah ──────────────────────────────────────────────────
INSERT INTO `rumah` (`rt_id`,`kk_id`,`no_urut`,`alamat`,`status_hunian`) VALUES
(1,1,'001','Jl. Mawar No. 1, RT001 RW015','milik'),
(2,2,'001','Jl. Melati No. 5, RT002 RW015','milik'),
(3,3,'001','Jl. Anggrek No. 3, RT003 RW015','sewa'),
(4,4,'001','Jl. Kenanga No. 7, RT004 RW015','milik'),
(5,5,'001','Jl. Dahlia No. 2, RT005 RW015','kontrak'),
(6,6,'001','Jl. Flamboyan No. 9, RT006 RW015','milik'),
(7,7,'001','Jl. Bougenville No. 4, RT007 RW015','milik'),
(1,NULL,'002','Jl. Mawar No. 3, RT001 RW015','kosong');

-- ── users ──────────────────────────────────────────────────
-- password: Admin@rw015  (bcrypt $2y$12$...)
INSERT INTO `users` (`id`,`role_id`,`warga_id`,`name`,`username`,`email`,`password`,`is_active`) VALUES
(1,1,NULL,  'Administrator',    'admin',   'admin@smartrw015.id',
 '$2y$12$eImiTXuWVxfM37uY4JANjO.cHHnTMIb.kQGpYn1K8nKtdlk3sC/9u',1),
(2,2,1,     'Budi Santoso',     'budiS',   'budi.s@smartrw015.id',
 '$2y$12$eImiTXuWVxfM37uY4JANjO.cHHnTMIb.kQGpYn1K8nKtdlk3sC/9u',1),
(3,3,4,     'Ahmad Fauzi',      'ahmadF',  'ahmad.f@smartrw015.id',
 '$2y$12$eImiTXuWVxfM37uY4JANjO.cHHnTMIb.kQGpYn1K8nKtdlk3sC/9u',1),
(4,4,6,     'Dewi Rahayu',      'dewiR',   'dewi.r@smartrw015.id',
 '$2y$12$eImiTXuWVxfM37uY4JANjO.cHHnTMIb.kQGpYn1K8nKtdlk3sC/9u',1),
(5,4,7,     'Hendra Gunawan',   'hendraG', 'hendra.g@smartrw015.id',
 '$2y$12$eImiTXuWVxfM37uY4JANjO.cHHnTMIb.kQGpYn1K8nKtdlk3sC/9u',1);

-- Ketua RT masing-masing (update setelah users ada)
UPDATE `rt` SET `ketua_id` = NULL;  -- FK ketua_id ditambahkan via ALTER di bawah jika dibutuhkan

-- ── surat (master jenis surat) ─────────────────────────────
INSERT INTO `surat` (`id`,`kode`,`nama`,`syarat`,`is_active`) VALUES
(1,'SKDB','Surat Keterangan Domisili Bertempat Tinggal','KTP asli, KK asli',1),
(2,'SKTM','Surat Keterangan Tidak Mampu','KTP asli, KK asli, Surat pengantar RT',1),
(3,'SKU', 'Surat Keterangan Usaha','KTP asli, KK asli, Foto usaha',1),
(4,'SKK', 'Surat Keterangan Kelahiran','Akta kelahiran sementara, KTP orang tua, KK',1),
(5,'SKM', 'Surat Keterangan Meninggal','Surat keterangan dokter/RS, KTP almarhum, KK',1),
(6,'SKPINDAH','Surat Keterangan Pindah','KTP asli, KK asli, Surat keterangan RT asal',1),
(7,'SKKEHILANGAN','Surat Keterangan Kehilangan','KTP asli, laporan polisi',1);

-- ── pengajuan_surat ────────────────────────────────────────
INSERT INTO `pengajuan_surat`
(`surat_id`,`warga_id`,`keperluan`,`status`,`disetujui_oleh`,`disetujui_at`) VALUES
(1, 6,'Keperluan melamar pekerjaan','selesai',1,'2026-06-05 10:00:00'),
(2, 9,'Mengurus beasiswa anak','diproses',NULL,NULL),
(3, 4,'Keperluan perizinan usaha warung','pending',NULL,NULL);

-- ── pengaduan_kategori ───────────────────────────────────
INSERT INTO `pengaduan_kategori` (`id`,`name`,`slug`,`description`,`warna`,`icon`) VALUES
(1,'Keamanan','keamanan','Masalah keamanan lingkungan, kehilangan, dan tindak kriminal.','#dc3545','bi-shield-exclamation'),
(2,'Kebersihan','kebersihan','Masalah sampah, TPS, dan kebersihan area publik.','#198754','bi-trash3'),
(3,'Infrastruktur','infrastruktur','Jalan, drainase, lampu jalan, dan sarana umum lainnya.','#0d6efd','bi-cone-striped'),
(4,'Lingkungan','lingkungan','Isu penghijauan, polusi, dan keberlanjutan lingkungan.','#20c997','bi-tree'),
(5,'Layanan RW','layanan-rw','Keluhan layanan administrasi dan operasional RW.','#6f42c1','bi-building');

-- ── pengaduan ──────────────────────────────────────────────
INSERT INTO `pengaduan`
(`id`,`user_id`,`kategori_id`,`no_tiket`,`judul`,`deskripsi`,`lokasi`,`status`,`prioritas`,`sla_target_at`,`created_at`,`updated_at`) VALUES
(1,4,3,'PGD-202606-0001','Jalan RT003 berlubang',
 'Jalan di depan gang 3 RT003 berlubang cukup dalam dan berbahaya bagi pengendara motor.',
 'Gang 3 RT003','diproses_rt','tinggi','2026-06-25 09:00:00','2026-06-18 08:00:00','2026-06-18 10:00:00'),
(2,5,2,'PGD-202606-0002','Sampah menumpuk di TPS RT005',
 'TPS di ujung gang belum diangkut sejak 3 hari lalu, bau tidak sedap dan mengundang lalat.',
 'TPS RT005','diproses_rw','sedang','2026-06-22 09:00:00','2026-06-17 07:30:00','2026-06-18 09:15:00'),
(3,2,1,'PGD-202606-0003','Lampu jalan mati RT007',
 'Lampu jalan di ujung gang RT007 sudah mati lebih dari seminggu, rawan kejahatan di malam hari.',
 'Ujung gang RT007 dekat taman','diterima','darurat','2026-06-19 07:00:00','2026-06-18 06:45:00','2026-06-18 06:45:00');

INSERT INTO `pengaduan_foto` (`pengaduan_id`,`foto_path`,`created_at`) VALUES
(1,'pengaduan/sample-jalan-rt003.jpg','2026-06-18 08:05:00'),
(2,'pengaduan/sample-tps-rt005.png','2026-06-17 07:35:00'),
(3,'pengaduan/sample-lampu-rt007.jpg','2026-06-18 06:50:00');

INSERT INTO `pengaduan_komentar` (`pengaduan_id`,`user_id`,`komentar`,`created_at`,`updated_at`) VALUES
(1,4,'Mohon segera diperbaiki karena sudah ada warga yang hampir terjatuh.','2026-06-18 08:10:00','2026-06-18 08:10:00'),
(1,3,'RT sudah menjadwalkan survey lapangan siang ini.','2026-06-18 09:15:00','2026-06-18 09:15:00'),
(2,2,'Mohon diprioritaskan karena sudah mengganggu kesehatan warga sekitar.','2026-06-17 07:40:00','2026-06-17 07:40:00');

INSERT INTO `pengaduan_disposisi_rt` (`pengaduan_id`,`rt_id`,`catatan`,`jadwal_penanganan`,`petugas_id`,`created_at`) VALUES
(1,3,'Akan dilakukan verifikasi lapangan dan koordinasi dengan petugas lingkungan.','2026-06-18 14:00:00',3,'2026-06-18 09:00:00'),
(2,3,'Masalah berulang dan membutuhkan dukungan armada dari RW.','2026-06-18 10:00:00',NULL,'2026-06-18 08:30:00');

INSERT INTO `pengaduan_disposisi_rw` (`pengaduan_id`,`rw_id`,`catatan`,`keputusan`,`alokasi_budget`,`departemen`,`created_at`) VALUES
(2,2,'Disetujui untuk penambahan armada angkut sampah dan pengawasan TPS.','approve',750000.00,'Kebersihan Lingkungan','2026-06-18 09:00:00');

INSERT INTO `pengaduan_status_history` (`pengaduan_id`,`status_lama`,`status_baru`,`keterangan`,`changed_by`,`changed_at`) VALUES
(1,NULL,'diterima','Pengaduan dibuat oleh warga.',4,'2026-06-18 08:00:00'),
(1,'diterima','diproses_rt','RT memverifikasi laporan dan menyiapkan survey. ',3,'2026-06-18 09:00:00'),
(2,NULL,'diterima','Pengaduan dibuat oleh warga.',5,'2026-06-17 07:30:00'),
(2,'diterima','diproses_rt','RT menerima laporan dan meminta tindak lanjut. ',3,'2026-06-18 08:30:00'),
(2,'diproses_rt','diproses_rw','Diteruskan ke RW untuk dukungan sumber daya. ',2,'2026-06-18 09:00:00'),
(3,NULL,'diterima','Pengaduan dibuat oleh warga.',2,'2026-06-18 06:45:00');

-- ── kegiatan ───────────────────────────────────────────────
INSERT INTO `kegiatan`
(`rt_id`,`judul`,`deskripsi`,`tanggal`,`waktu_mulai`,`waktu_selesai`,`lokasi`,`is_published`,`dibuat_oleh`) VALUES
(NULL,'Kerja Bakti Bulanan RW015',
 'Kegiatan bersih-bersih seluruh lingkungan RW015 secara gotong royong.',
 '2026-06-22','07:00:00','10:00:00','Seluruh Lingkungan RW015',1,2),
(NULL,'Rapat Koordinasi RT/RW',
 'Rapat pengurus RT 001–007 dan RW015 membahas program kerja semester 2.',
 '2026-06-25','19:30:00','21:30:00','Balai RW015',1,2),
(NULL,'Posyandu Rutin Balita',
 'Penimbangan, imunisasi, dan pemeriksaan kesehatan balita bulanan.',
 '2026-06-28','08:00:00','11:00:00','Posyandu RW015',1,2),
(1,'Rapat Warga RT001',
 'Rapat internal warga RT001 membahas iuran dan keamanan lingkungan.',
 '2026-07-03','19:30:00','21:00:00','Rumah Ketua RT001',1,3);

-- ── kas_rw ─────────────────────────────────────────────────
INSERT INTO `kas_rw`
(`tanggal`,`jenis`,`kategori`,`keterangan`,`jumlah`,`saldo_setelah`,`dibuat_oleh`) VALUES
('2026-06-01','pemasukan','Iuran Warga','Iuran bulanan Juni 2026 – 120 KK x Rp25.000',
  3000000.00, 3000000.00, 2),
('2026-06-03','pemasukan','Donasi','Donasi kegiatan HUT RI dari warga',
   500000.00, 3500000.00, 2),
('2026-06-10','pengeluaran','Kebersihan','Pembelian sapu, sekop, dan karung sampah',
   350000.00, 3150000.00, 2),
('2026-06-15','pengeluaran','Administrasi','ATK, fotokopi, dan materai kantor RW',
   175000.00, 2975000.00, 2),
('2026-06-20','pengeluaran','Sosial','Santunan warga duka cita',
   500000.00, 2475000.00, 2);

-- ── kas_rt ─────────────────────────────────────────────────
INSERT INTO `kas_rt`
(`rt_id`,`tanggal`,`jenis`,`kategori`,`keterangan`,`jumlah`,`saldo_setelah`,`dibuat_oleh`) VALUES
(1,'2026-06-01','pemasukan','Iuran Warga','Iuran RT001 Juni 2026',
   600000.00, 600000.00, 3),
(1,'2026-06-12','pengeluaran','Keamanan','Beli senter & baterai ronda',
    75000.00, 525000.00, 3),
(2,'2026-06-01','pemasukan','Iuran Warga','Iuran RT002 Juni 2026',
   700000.00, 700000.00, 2),
(2,'2026-06-15','pengeluaran','Kebersihan','Pengadaan tempat sampah pilah',
   200000.00, 500000.00, 2);

-- ── inventaris ─────────────────────────────────────────────
INSERT INTO `inventaris`
(`kode_barang`,`nama`,`kategori`,`kondisi`,`jumlah`,`satuan`,`lokasi`,`tanggal_pengadaan`,`nilai_perolehan`) VALUES
('INV-001','Meja Rapat','Furnitur','baik',2,'unit','Balai RW015','2023-01-15',1200000.00),
('INV-002','Kursi Plastik','Furnitur','baik',30,'unit','Balai RW015','2023-01-15',150000.00),
('INV-003','Proyektor Infocus','Elektronik','baik',1,'unit','Balai RW015','2023-03-10',4500000.00),
('INV-004','Toa / Pengeras Suara','Elektronik','baik',1,'set','Balai RW015','2022-08-20',800000.00),
('INV-005','Gerobak Sampah','Alat Kebersihan','baik',3,'unit','Pos Sampah RW015','2024-02-01',600000.00),
('INV-006','Timbangan Berat Badan','Alat Kesehatan','baik',1,'unit','Posyandu RW015','2023-06-01',350000.00),
('INV-007','Meteran Tinggi Badan','Alat Kesehatan','baik',1,'unit','Posyandu RW015','2023-06-01',120000.00),
('INV-008','HT (Handy Talky)','Elektronik','rusak_ringan',2,'unit','Pos Satpam','2021-11-05',750000.00);

-- ── umkm ───────────────────────────────────────────────────
INSERT INTO `umkm`
(`warga_id`,`rt_id`,`nama_usaha`,`kategori`,`deskripsi`,`no_hp`,`status`,`dibuat_oleh`) VALUES
(4,2,'Warung Pak Ahmad','Kuliner',
 'Warung makan dan kelontong, buka setiap hari 06.00–21.00','085212345678','aktif',2),
(11,7,'Toko Teguh Jaya','Kuliner',
 'Toko sembako dan kebutuhan sehari-hari','085287654321','aktif',2),
(6,3,'Jasa Jahit Dewi','Jasa',
 'Jasa jahit dan permak pakaian, menerima pesanan seragam dan batik','082312345678','aktif',2),
(10,6,'Lia Beauty Care','Jasa',
 'Salon kecantikan dan perawatan rambut','081234567890','aktif',2);

-- ── posyandu ───────────────────────────────────────────────
INSERT INTO `posyandu`
(`warga_id`,`tanggal`,`jenis_kegiatan`,`berat_badan`,`tinggi_badan`,`lingkar_kepala`,`status_gizi`,`petugas_id`) VALUES
( 3,'2026-06-28','balita', 14.5,  98.0, 49.5,'Gizi Baik',   1),
( 2,'2026-06-28','ibu_hamil',70.0,160.0, NULL,'Normal',      1),
(12,'2026-06-28','ibu_hamil',65.5,158.0, NULL,'Normal',      1),
( 8,'2026-06-28','lansia',  58.0, 155.0, NULL,'Gizi Baik',   1);

-- ── security ───────────────────────────────────────────────
INSERT INTO `security`
(`rt_id`,`tanggal`,`petugas`,`shift`,`jam_mulai`,`jam_selesai`,`status`,`dibuat_oleh`) VALUES
(NULL,'2026-06-18','Pak Rudi Hermawan',  'malam','22:00:00','05:00:00','selesai',2),
(NULL,'2026-06-18','Pak Teguh Prasetyo', 'malam','22:00:00','05:00:00','selesai',2),
(NULL,'2026-06-19','Pak Budi Santoso',   'malam','22:00:00','05:00:00','terjadwal',2),
(1,   '2026-06-19','Pak Hendra Gunawan', 'malam','22:00:00','05:00:00','terjadwal',2);

-- ── pengumuman ─────────────────────────────────────────────
INSERT INTO `pengumuman`
(`rt_id`,`judul`,`isi`,`jenis`,`is_published`,`published_at`,`dibuat_oleh`) VALUES
(NULL,
 'Jadwal Kerja Bakti Bulan Juni 2026',
 'Warga RW015 yang terhormat,\n\nDiinformasikan bahwa kerja bakti bulanan akan dilaksanakan pada:\n\nHari/Tanggal : Senin, 22 Juni 2026\nWaktu        : 07.00 WIB s/d selesai\nLokasi       : Seluruh lingkungan RW015\n\nKehadiran seluruh warga sangat diharapkan. Harap membawa peralatan kebersihan masing-masing.\n\nTerima kasih.\n\nHormat kami,\nPengurus RW015',
 'penting',1,'2026-06-16 08:00:00',2),
(NULL,
 'Pengumuman Posyandu Rutin Juni 2026',
 'Posyandu rutin balita dan ibu hamil bulan Juni 2026 akan dilaksanakan pada:\n\nHari/Tanggal : Sabtu, 28 Juni 2026\nWaktu        : 08.00 – 11.00 WIB\nLokasi       : Posyandu RW015\n\nMohon membawa buku KIA / KMS masing-masing.',
 'umum',1,'2026-06-17 09:00:00',2),
(1,
 '[RT001] Rapat Warga – Iuran & Keamanan',
 'Rapat warga RT001 akan dilaksanakan pada Kamis, 3 Juli 2026 pukul 19.30 WIB di kediaman Ketua RT001. Agenda: evaluasi iuran dan program ronda malam.',
 'umum',1,'2026-06-18 07:00:00',3);

-- ── notifikasi ─────────────────────────────────────────────
INSERT INTO `notifikasi`
(`user_id`,`judul`,`pesan`,`tipe`,`is_read`) VALUES
(NULL,'Pengumuman Baru',
 'Pengumuman jadwal kerja bakti bulan Juni 2026 telah dipublikasikan.','info',0),
(4,  'Pengajuan Surat Selesai',
 'Pengajuan Surat Keterangan Domisili Anda telah selesai diproses. Silakan ambil di kantor RW.','sukses',0),
(5,  'Selamat Datang di Smart RW015',
 'Akun Anda telah berhasil didaftarkan. Selamat menggunakan layanan Smart RW015.','info',0);

-- ── log_aktivitas ──────────────────────────────────────────
INSERT INTO `log_aktivitas`
(`user_id`,`aksi`,`modul`,`data_id`,`keterangan`,`ip_address`) VALUES
(1,'create','roles',    1,'Seed awal data roles',    '127.0.0.1'),
(1,'create','rw',       1,'Seed awal data RW015',    '127.0.0.1'),
(1,'create','rt',       1,'Seed awal data RT 001–007','127.0.0.1'),
(2,'approve','pengajuan_surat',1,'Menyetujui SKDB warga Dewi Rahayu','192.168.1.10'),
(2,'create','pengumuman',1,'Publikasi pengumuman kerja bakti','192.168.1.10');

SET FOREIGN_KEY_CHECKS = 1;
