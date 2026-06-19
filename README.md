# Smart RW015 Taman Cikarang Indah 2

Portal digital RW015 Taman Cikarang Indah 2 berbasis **PHP 8.2 + MySQL 8 + Bootstrap 5** dengan arsitektur **MVC** dan siap dijalankan di **XAMPP**.

## Modul Utama

1. Authentication
2. RBAC
3. Dashboard RW
4. Dashboard RT
5. Administrasi Penduduk
6. Kartu Keluarga
7. Rumah
8. Surat Online
9. Kas RW
10. Kas RT
11. Kegiatan
12. Pengaduan
13. Posyandu
14. Security
15. UMKM
16. Pengumuman
17. Notifikasi WhatsApp (stub integrasi)
18. Laporan PDF / Excel (PDF print-ready + CSV)
19. Audit Log

## Struktur Project

- `app/` — controller, model, view, core MVC
- `config/` — konfigurasi aplikasi, database, WhatsApp stub
- `database/schema.sql` — schema MySQL lengkap
- `database/seeder.sql` — dummy data RW015 dan RT001–RT007
- `public/assets/` — asset aplikasi
- `storage/uploads/` — file upload

## Persyaratan

- PHP 8.2+
- MySQL 8+
- XAMPP terbaru
- Ekstensi PHP: `pdo`, `pdo_mysql`, `mbstring`

## Deployment di XAMPP

1. Salin folder project ke `htdocs`, misalnya:
   - `C:/xampp/htdocs/smartrw015-tamancikarangindah2`
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Buat database baru bernama `smartrw015`.
4. Import schema:
   - buka `phpMyAdmin`
   - pilih database `smartrw015`
   - import file `database/schema.sql`
5. Import dummy data:
   - import file `database/seeder.sql`
6. Sesuaikan `config/config.php` dan `config/database.php` bila URL atau kredensial MySQL berbeda.
7. Akses aplikasi di browser:
   - `http://localhost/smartrw015-tamancikarangindah2`

## Konfigurasi Default

### Database

File: `config/database.php`

- Host: `localhost`
- Port: `3306`
- Database: `smartrw015`
- User: `root`
- Password: kosong (`''`) untuk default XAMPP

### Session & Security

- CSRF token di semua form utama
- Output escaping untuk perlindungan XSS
- Password hashing `PASSWORD_BCRYPT`
- Session cookie `httponly` + `SameSite=Lax`
- Audit log aktivitas pengguna

### WhatsApp Stub

File: `config/whatsapp.php`

Secara default integrasi WhatsApp dalam mode stub (`enabled => false`).
Endpoint API tersedia untuk dihubungkan ke gateway WA riil:

- `POST /api/notifications/whatsapp/pengumuman`
- `POST /api/notifications/whatsapp/surat`

## Akun Dummy Seeder

Password seluruh akun dummy: `Admin@rw015`

- Administrator: `admin`
- Pengurus RW: `budiS`
- Pengurus RT: `ahmadF`
- Warga: `dewiR`

## Database Dummy

Seeder berisi data contoh:

- RW015
- RT001 sampai RT007
- KK, warga, rumah
- transaksi kas RW & RT
- pengajuan surat
- pengaduan warga
- kegiatan, posyandu, security
- UMKM, pengumuman, notifikasi
- audit log awal

## Fitur Laporan

Halaman laporan tersedia di `/laporan`.

- **PDF**: mode print-ready untuk disimpan sebagai PDF dari browser
- **Excel**: diekspor sebagai CSV yang kompatibel dengan Excel

## Validasi PHP

Untuk pengecekan syntax lokal:

```bash
find app config routes -name '*.php' -print0 | xargs -0 -n1 php -l && php -l index.php
```

## Catatan

Project ini disusun sebagai scaffold operasional lengkap untuk lingkungan RW/RT dengan data dummy dan struktur MVC yang siap dikembangkan lebih lanjut.
