<?php

/**
 * WhatsAppNotificationService
 * Menyediakan template notifikasi WhatsApp untuk event utama aplikasi.
 */

declare(strict_types=1);

require_once APP_PATH . '/helpers/WhatsAppGatewayClient.php';

class WhatsAppNotificationService
{
    private WhatsAppGatewayClient $gateway;

    public function __construct()
    {
        $this->gateway = new WhatsAppGatewayClient();
    }

    public function notifyPengajuanSurat(string $phone, array $data = []): array
    {
        $jenisSurat = trim((string) ($data['jenis_surat'] ?? 'surat'));
        $namaWarga = trim((string) ($data['nama_warga'] ?? 'warga'));
        $nomor = trim((string) ($data['nomor_pengajuan'] ?? '-'));

        $message = sprintf(
            'Pengajuan %s atas nama %s telah diterima dengan nomor %s. Mohon tunggu proses verifikasi dari pengurus RW/RT.',
            $jenisSurat,
            $namaWarga,
            $nomor
        );

        return $this->gateway->sendMessage($phone, $message, [
            'event' => 'pengajuan_surat',
            'data' => $data,
        ]);
    }

    public function notifyApprovalSurat(string $phone, array $data = []): array
    {
        $jenisSurat = trim((string) ($data['jenis_surat'] ?? 'surat'));
        $status = trim((string) ($data['status'] ?? 'diproses'));
        $catatan = trim((string) ($data['catatan'] ?? ''));

        $message = sprintf(
            'Update pengajuan %s: status saat ini %s.',
            $jenisSurat,
            $status
        );

        if ($catatan !== '') {
            $message .= ' Catatan: ' . $catatan;
        }

        return $this->gateway->sendMessage($phone, $message, [
            'event' => 'approval_surat',
            'data' => $data,
        ]);
    }

    public function notifyPengumuman(string $phone, array $data = []): array
    {
        $judul = trim((string) ($data['judul'] ?? 'Pengumuman RW'));
        $ringkasan = trim((string) ($data['ringkasan'] ?? 'Silakan cek aplikasi untuk detail pengumuman terbaru.'));

        $message = sprintf('Pengumuman baru: %s. %s', $judul, $ringkasan);

        return $this->gateway->sendMessage($phone, $message, [
            'event' => 'pengumuman',
            'data' => $data,
        ]);
    }

    public function notifyIuranBulanan(string $phone, array $data = []): array
    {
        $periode = trim((string) ($data['periode'] ?? date('F Y')));
        $nominal = trim((string) ($data['nominal'] ?? '-'));
        $jatuhTempo = trim((string) ($data['jatuh_tempo'] ?? '-'));

        $message = sprintf(
            'Pengingat iuran bulanan periode %s. Nominal: %s. Jatuh tempo: %s.',
            $periode,
            $nominal,
            $jatuhTempo
        );

        return $this->gateway->sendMessage($phone, $message, [
            'event' => 'iuran_bulanan',
            'data' => $data,
        ]);
    }

    public function notifyJadwalKegiatan(string $phone, array $data = []): array
    {
        $judul = trim((string) ($data['judul'] ?? 'Kegiatan warga'));
        $tanggal = trim((string) ($data['tanggal'] ?? '-'));
        $lokasi = trim((string) ($data['lokasi'] ?? '-'));

        $message = sprintf(
            'Jadwal kegiatan: %s pada %s di %s. Mohon kehadirannya.',
            $judul,
            $tanggal,
            $lokasi
        );

        return $this->gateway->sendMessage($phone, $message, [
            'event' => 'jadwal_kegiatan',
            'data' => $data,
        ]);
    }
}
