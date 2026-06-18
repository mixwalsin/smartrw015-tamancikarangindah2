<?php

declare(strict_types=1);

require_once APP_PATH . '/repositories/PengaduanRepository.php';
require_once APP_PATH . '/services/NotificationService.php';

class DisposisiService
{
    private PengaduanRepository $repository;
    private NotificationService $notifications;

    public function __construct()
    {
        $this->repository = new PengaduanRepository();
        $this->notifications = new NotificationService();
    }

    public function submitRtDisposition(int $pengaduanId, array $data, array $actor): void
    {
        $detail = $this->requirePengaduan($pengaduanId);

        $this->repository->addRtDisposition([
            'pengaduan_id' => $pengaduanId,
            'rt_id' => $actor['id'],
            'catatan' => trim((string) ($data['catatan'] ?? '')),
            'jadwal_penanganan' => ($data['jadwal_penanganan'] ?? '') !== '' ? $data['jadwal_penanganan'] : null,
            'petugas_id' => ($data['petugas_id'] ?? '') !== '' ? (int) $data['petugas_id'] : null,
        ]);

        $status = !empty($data['teruskan_ke_rw']) ? 'diproses_rw' : 'diproses_rt';
        $note = !empty($data['teruskan_ke_rw']) ? 'Diteruskan ke RW oleh RT.' : 'RT menetapkan tindak lanjut penanganan.';
        $this->repository->updateStatus($pengaduanId, $status, $note);
        $this->repository->addHistory([
            'pengaduan_id' => $pengaduanId,
            'status_lama' => $detail['status'],
            'status_baru' => $status,
            'keterangan' => $note,
            'changed_by' => $actor['id'],
            'changed_at' => date('Y-m-d H:i:s'),
        ]);

        $this->notifications->notifyComplaintEvent($detail, 'rt_disposition', (int) $detail['user_id']);
    }

    public function submitRwDisposition(int $pengaduanId, array $data, array $actor): void
    {
        $detail = $this->requirePengaduan($pengaduanId);

        $keputusan = trim((string) ($data['keputusan'] ?? 'review'));
        $status = $keputusan === 'reject' ? 'ditolak' : ($keputusan === 'approve' ? 'dalam_perbaikan' : 'diproses_rw');
        $keterangan = trim((string) ($data['catatan'] ?? 'Review RW diperbarui.'));

        $this->repository->addRwDisposition([
            'pengaduan_id' => $pengaduanId,
            'rw_id' => $actor['id'],
            'catatan' => $keterangan,
            'keputusan' => $keputusan,
            'alokasi_budget' => ($data['alokasi_budget'] ?? '') !== '' ? (float) $data['alokasi_budget'] : null,
            'departemen' => trim((string) ($data['departemen'] ?? '')) ?: null,
        ]);

        $this->repository->updateStatus(
            $pengaduanId,
            $status,
            $keterangan,
            $status === 'ditolak' ? $keterangan : null,
        );
        $this->repository->addHistory([
            'pengaduan_id' => $pengaduanId,
            'status_lama' => $detail['status'],
            'status_baru' => $status,
            'keterangan' => $keterangan,
            'changed_by' => $actor['id'],
            'changed_at' => date('Y-m-d H:i:s'),
        ]);

        $this->notifications->notifyComplaintEvent($detail, 'rw_disposition', (int) $detail['user_id']);
    }

    private function requirePengaduan(int $id): array
    {
        $detail = $this->repository->findDetail($id);
        if (!$detail) {
            throw new RuntimeException('Pengaduan tidak ditemukan.');
        }

        return $detail;
    }
}
