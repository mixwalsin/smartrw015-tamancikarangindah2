
# Smart RW015 Taman Cikarang Indah 2

Portal Digital RW015 Taman Cikarang Indah 2

## Fitur

- Dashboard RW
- Dashboard RT
- Administrasi Penduduk
- Surat Menyurat
- Keuangan RW
- Pengaduan Warga
- Kegiatan RW
- UMKM
- Posyandu
- Security
- Notifikasi WA
- Statistik Penduduk

Technology:
- PHP 8.2
- MySQL
- Bootstrap 5
- XAMPP

## Integrasi WhatsApp Gateway

Konfigurasi gateway diambil dari environment variable (lihat `config/whatsapp.php`):

- `WA_GATEWAY_ENABLED` (true/false)
- `WA_GATEWAY_ENDPOINT`
- `WA_GATEWAY_API_KEY`
- `WA_GATEWAY_AUTH_HEADER` (default: `Authorization`)
- `WA_GATEWAY_AUTH_SCHEME` (default: `Bearer`)
- `WA_GATEWAY_TIMEOUT` (default: 15 detik)
- `WA_GATEWAY_CONNECT_TIMEOUT` (default: 5 detik)
- `WA_GATEWAY_RECIPIENT_FIELD` (default: `phone`)
- `WA_GATEWAY_MESSAGE_FIELD` (default: `message`)
- `WA_GATEWAY_METADATA_FIELD` (default: `metadata`)
- `WA_GATEWAY_EXTRA_PAYLOAD` (JSON object opsional)
- `WA_NOTIFICATION_API_KEY` (opsional, untuk proteksi endpoint trigger)

Endpoint trigger notifikasi WhatsApp:

- `POST /api/notifications/whatsapp/pengajuan-surat`
- `POST /api/notifications/whatsapp/approval-surat`
- `POST /api/notifications/whatsapp/pengumuman`
- `POST /api/notifications/whatsapp/iuran-bulanan`
- `POST /api/notifications/whatsapp/jadwal-kegiatan`

Payload minimal:

- `phone` atau `no_hp`
- `data` (object opsional sesuai kebutuhan template pesan)