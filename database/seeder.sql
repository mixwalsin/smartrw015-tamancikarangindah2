-- ============================================================
-- Smart RW015 - Sample / Seed Data
-- ============================================================

USE `smartrw015`;

-- Contoh data penduduk
INSERT INTO `penduduk` (`nik`,`no_kk`,`nama`,`tempat_lahir`,`tanggal_lahir`,`jenis_kelamin`,`alamat`,`rt`,`rw`,`status_kawin`,`agama`,`pekerjaan`) VALUES
('3216051001010001','3216051001010001','Budi Santoso','Cikarang','1985-01-15','L','Jl. Mawar No. 1','01','015','Kawin','Islam','Karyawan Swasta'),
('3216051001010002','3216051001010001','Siti Aminah','Bekasi','1990-03-20','P','Jl. Mawar No. 1','01','015','Kawin','Islam','Ibu Rumah Tangga'),
('3216051001010003','3216051001010002','Ahmad Fauzi','Jakarta','1978-07-10','L','Jl. Melati No. 5','02','015','Kawin','Islam','Wirausaha'),
('3216051001010004','3216051001010003','Dewi Rahayu','Bandung','1995-11-25','P','Jl. Anggrek No. 3','03','015','Belum Kawin','Islam','Mahasiswa'),
('3216051001010005','3216051001010004','Hendra Gunawan','Cikarang','1982-04-05','L','Jl. Kenanga No. 7','04','015','Kawin','Kristen','PNS');

-- Contoh data kegiatan
INSERT INTO `kegiatan` (`judul`,`deskripsi`,`tanggal`,`waktu`,`lokasi`,`user_id`) VALUES
('Kerja Bakti Bulanan','Kegiatan bersih-bersih lingkungan RW015','2026-06-22','07:00:00','Lapangan RW015', 1),
('Rapat RT/RW','Rapat koordinasi pengurus RT dan RW','2026-06-25','19:30:00','Balai RW015', 1),
('Posyandu Balita','Penimbangan dan imunisasi balita','2026-06-28','08:00:00','Posyandu RW015', 1);

-- Contoh data keuangan
INSERT INTO `keuangan` (`tanggal`,`jenis`,`kategori`,`keterangan`,`jumlah`,`user_id`) VALUES
('2026-06-01','pemasukan','Iuran Warga','Iuran bulanan Juni 2026',2500000.00, 1),
('2026-06-05','pemasukan','Donasi','Donasi kegiatan lingkungan',500000.00, 1),
('2026-06-10','pengeluaran','Kebersihan','Pembelian alat kebersihan',350000.00, 1),
('2026-06-15','pengeluaran','Administrasi','ATK dan fotokopi',150000.00, 1);
