<?php

declare(strict_types=1);

require_once APP_PATH . '/models/NotifikasiModel.php';

class NotificationService
{
    private NotifikasiModel $model;

    public function __construct()
    {
        $this->model = new NotifikasiModel();
    }

    public function notifyComplaintEvent(array $pengaduan, string $event, ?int $targetUserId = null): void
    {
        $messages = [
            'created' => 'Pengaduan baru telah diterima sistem dan menunggu verifikasi.',
            'status_updated' => 'Status pengaduan Anda telah diperbarui.',
            'comment_added' => 'Ada komentar atau tindak lanjut baru pada pengaduan Anda.',
            'rt_disposition' => 'RT telah menambahkan disposisi pada pengaduan Anda.',
            'rw_disposition' => 'RW telah menambahkan keputusan pada pengaduan Anda.',
        ];

        $this->model->insert([
            'user_id' => $targetUserId,
            'judul' => 'Pengaduan #' . ($pengaduan['no_tiket'] ?? $pengaduan['id'] ?? '-'),
            'pesan' => $messages[$event] ?? 'Ada pembaruan pengaduan baru.',
            'tipe' => in_array($event, ['created', 'status_updated'], true) ? 'info' : 'sukses',
            'url' => url('pengaduan/show/' . ($pengaduan['id'] ?? '')),
            'is_read' => 0,
        ]);
    }

    public function latest(?int $userId, int $limit = 8): array
    {
        return $this->model->latestForUser($userId, $limit);
    }

    public function buildStatusEmail(array $pengaduan, string $label): string
    {
        ob_start();
        $statusLabel = $label;
        require APP_PATH . '/views/pengaduan/emails/status_update.php';
        return (string) ob_get_clean();
    }
}
