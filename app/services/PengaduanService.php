<?php

declare(strict_types=1);

require_once APP_PATH . '/models/LogAktivitasModel.php';
require_once APP_PATH . '/models/UserModel.php';
require_once APP_PATH . '/repositories/PengaduanKategoriRepository.php';
require_once APP_PATH . '/repositories/PengaduanRepository.php';
require_once APP_PATH . '/services/FileUploadService.php';
require_once APP_PATH . '/services/NotificationService.php';
require_once APP_PATH . '/services/ReportService.php';

class PengaduanService
{
    private PengaduanRepository $repository;
    private PengaduanKategoriRepository $kategoriRepository;
    private FileUploadService $uploads;
    private NotificationService $notifications;
    private ReportService $reports;
    private LogAktivitasModel $logs;
    private UserModel $users;

    public function __construct()
    {
        $this->repository = new PengaduanRepository();
        $this->kategoriRepository = new PengaduanKategoriRepository();
        $this->uploads = new FileUploadService();
        $this->notifications = new NotificationService();
        $this->reports = new ReportService();
        $this->logs = new LogAktivitasModel();
        $this->users = new UserModel();
    }

    public function dashboard(array $filters, int $page, array $actor): array
    {
        $actor = $this->hydrateActor($actor);

        return [
            'pagination' => $this->repository->paginate($filters, $page, $actor),
            'summary' => $this->repository->summary($actor),
            'categories' => $this->kategoriRepository->all(),
            'notifications' => $this->notifications->latest($actor['id'] ?? null),
        ];
    }

    public function create(array $payload, array $files, array $actor): int
    {
        $actor = $this->hydrateActor($actor);
        $kategoriId = (int) ($payload['kategori_id'] ?? 0);
        $kategori = $this->kategoriRepository->find($kategoriId);
        if (!$kategori) {
            throw new RuntimeException('Kategori pengaduan wajib dipilih.');
        }

        $judul = trim((string) ($payload['judul'] ?? ''));
        $deskripsi = trim((string) ($payload['deskripsi'] ?? ''));
        if ($judul === '' || $deskripsi === '') {
            throw new RuntimeException('Judul dan deskripsi pengaduan wajib diisi.');
        }

        $prioritas = (string) ($payload['prioritas'] ?? 'sedang');
        $ticket = $this->generateTicketNumber();
        $slaDays = PENGADUAN_SLA_BY_CATEGORY[$kategori['slug']] ?? PENGADUAN_DEFAULT_SLA_DAYS;
        $pengaduanId = $this->repository->create([
            'user_id' => (int) ($actor['id'] ?? 0),
            'kategori_id' => $kategoriId,
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'status' => 'diterima',
            'prioritas' => $prioritas,
            'lokasi' => trim((string) ($payload['lokasi'] ?? '')) ?: null,
            'no_tiket' => $ticket,
            'sla_target_at' => date('Y-m-d H:i:s', strtotime('+' . $slaDays . ' days')),
        ]);

        foreach ($this->uploads->uploadComplaintPhotos($files) as $path) {
            $this->repository->addPhoto([
                'pengaduan_id' => $pengaduanId,
                'foto_path' => $path,
            ]);
        }

        $this->repository->addHistory([
            'pengaduan_id' => $pengaduanId,
            'status_lama' => null,
            'status_baru' => 'diterima',
            'keterangan' => 'Pengaduan dibuat oleh warga.',
            'changed_by' => (int) ($actor['id'] ?? 0),
            'changed_at' => date('Y-m-d H:i:s'),
        ]);

        $pengaduan = $this->repository->findDetail($pengaduanId);
        if ($pengaduan) {
            $this->notifications->notifyComplaintEvent($pengaduan, 'created', (int) $pengaduan['user_id']);
        }

        $this->log('create', $pengaduanId, 'Membuat pengaduan baru #' . $ticket, $actor);

        return $pengaduanId;
    }

    public function findDetail(int $id, array $actor): array|false
    {
        $actor = $this->hydrateActor($actor);
        $detail = $this->repository->findDetail($id);
        if (!$detail) {
            return false;
        }

        if (!$this->canAccess($detail, $actor)) {
            throw new RuntimeException('Anda tidak memiliki akses ke pengaduan ini.');
        }

        return $detail;
    }

    public function addComment(int $pengaduanId, array $payload, array $files, array $actor): void
    {
        $detail = $this->findDetail($pengaduanId, $actor);
        if (!$detail) {
            throw new RuntimeException('Pengaduan tidak ditemukan.');
        }

        $komentar = trim((string) ($payload['komentar'] ?? ''));
        if ($komentar === '') {
            throw new RuntimeException('Komentar wajib diisi.');
        }

        $attachment = $this->uploads->uploadCommentAttachment($files['lampiran'] ?? null);
        $this->repository->addComment([
            'pengaduan_id' => $pengaduanId,
            'user_id' => (int) ($actor['id'] ?? 0),
            'komentar' => $komentar,
            'lampiran_path' => $attachment,
        ]);

        $this->notifications->notifyComplaintEvent($detail, 'comment_added', (int) $detail['user_id']);
        $this->log('comment', $pengaduanId, 'Menambahkan komentar pengaduan', $actor);
    }

    public function updateComment(int $commentId, array $payload, array $actor): void
    {
        $comment = $this->repository->findComment($commentId);
        if (!$comment) {
            throw new RuntimeException('Komentar tidak ditemukan.');
        }

        if ((int) $comment['user_id'] !== (int) ($actor['id'] ?? 0) && !$this->isManager($actor)) {
            throw new RuntimeException('Anda tidak dapat mengubah komentar ini.');
        }

        $komentar = trim((string) ($payload['komentar'] ?? ''));
        if ($komentar === '') {
            throw new RuntimeException('Komentar wajib diisi.');
        }

        $this->repository->updateComment($commentId, ['komentar' => $komentar]);
    }

    public function deleteComment(int $commentId, array $actor): void
    {
        $comment = $this->repository->findComment($commentId);
        if (!$comment) {
            throw new RuntimeException('Komentar tidak ditemukan.');
        }

        if ((int) $comment['user_id'] !== (int) ($actor['id'] ?? 0) && !$this->isManager($actor)) {
            throw new RuntimeException('Anda tidak dapat menghapus komentar ini.');
        }

        $this->repository->deleteComment($commentId);
    }

    public function updateStatus(int $pengaduanId, array $payload, array $actor): void
    {
        $detail = $this->findDetail($pengaduanId, $actor);
        if (!$detail) {
            throw new RuntimeException('Pengaduan tidak ditemukan.');
        }

        $newStatus = (string) ($payload['status'] ?? '');
        if (!array_key_exists($newStatus, PENGADUAN_STATUSES)) {
            throw new RuntimeException('Status pengaduan tidak valid.');
        }

        $this->validateStatusTransition((string) $detail['status'], $newStatus);
        $note = trim((string) ($payload['keterangan'] ?? ''));
        $rejectionReason = $newStatus === 'ditolak' ? $note : null;

        $this->repository->updateStatus($pengaduanId, $newStatus, $note, $rejectionReason);
        $this->repository->addHistory([
            'pengaduan_id' => $pengaduanId,
            'status_lama' => $detail['status'],
            'status_baru' => $newStatus,
            'keterangan' => $note,
            'changed_by' => (int) ($actor['id'] ?? 0),
            'changed_at' => date('Y-m-d H:i:s'),
        ]);

        $this->notifications->notifyComplaintEvent($detail, 'status_updated', (int) $detail['user_id']);
        $this->log('status', $pengaduanId, 'Mengubah status pengaduan menjadi ' . $newStatus, $actor);
    }

    public function addPhotos(int $pengaduanId, array $files, array $actor): void
    {
        $detail = $this->findDetail($pengaduanId, $actor);
        if (!$detail) {
            throw new RuntimeException('Pengaduan tidak ditemukan.');
        }

        foreach ($this->uploads->uploadComplaintPhotos($files) as $path) {
            $this->repository->addPhoto([
                'pengaduan_id' => $pengaduanId,
                'foto_path' => $path,
            ]);
        }
    }

    public function deletePhoto(int $photoId, array $actor): void
    {
        $photo = $this->repository->findPhoto($photoId);
        if (!$photo) {
            throw new RuntimeException('Foto tidak ditemukan.');
        }

        $detail = $this->findDetail((int) $photo['pengaduan_id'], $actor);
        if (!$detail || (!$this->isManager($actor) && (int) $detail['user_id'] !== (int) ($actor['id'] ?? 0))) {
            throw new RuntimeException('Anda tidak memiliki akses untuk menghapus foto ini.');
        }

        $absolutePath = $this->uploads->absolutePath((string) $photo['foto_path']);
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }

        $this->repository->removePhoto($photoId);
    }

    public function findPhoto(int $photoId, array $actor): array|false
    {
        $photo = $this->repository->findPhoto($photoId);
        if (!$photo) {
            return false;
        }

        $detail = $this->findDetail((int) $photo['pengaduan_id'], $actor);
        if (!$detail) {
            return false;
        }

        return $photo;
    }

    public function reports(array $actor): array
    {
        $actor = $this->hydrateActor($actor);
        return $this->reports->dashboard($actor);
    }

    public function exportRows(array $actor): array
    {
        $actor = $this->hydrateActor($actor);
        return $this->reports->rowsForExport($actor);
    }

    public function categories(): array
    {
        return $this->kategoriRepository->all();
    }

    public function statuses(): array
    {
        return PENGADUAN_STATUSES;
    }

    public function priorities(): array
    {
        return PENGADUAN_PRIORITAS;
    }

    private function canAccess(array $detail, array $actor): bool
    {
        $role = strtolower((string) ($actor['role'] ?? ''));
        if ($this->isManager($actor) || in_array($role, ['rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw'], true)) {
            return true;
        }

        if (in_array($role, ['rt', 'ketua_rt', 'admin_rt'], true)) {
            return !empty($actor['rt_id']) && (int) ($detail['rt_id'] ?? 0) === (int) $actor['rt_id'];
        }

        return (int) ($detail['user_id'] ?? 0) === (int) ($actor['id'] ?? 0);
    }

    private function isManager(array $actor): bool
    {
        return in_array(strtolower((string) ($actor['role'] ?? '')), ['admin', 'super_admin'], true);
    }

    private function hydrateActor(array $actor): array
    {
        if (!empty($actor['id'])) {
            $context = $this->users->findWithRoleContext((int) $actor['id']);
            if ($context) {
                $actor['role'] = $actor['role'] ?: ($context['role'] ?? '');
                $actor['warga_id'] = $actor['warga_id'] ?? ($context['warga_id'] ?? null);
                $actor['rt_id'] = $actor['rt_id'] ?? ($context['rt_id'] ?? null);
            }
        }

        return $actor;
    }

    private function generateTicketNumber(): string
    {
        $period = date('Ym');
        $sequence = $this->repository->nextTicketSequence($period);

        do {
            $ticket = 'PGD-' . $period . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while ($this->repository->ticketExists($ticket));

        return $ticket;
    }

    private function validateStatusTransition(string $from, string $to): void
    {
        $allowed = [
            'diterima' => ['diproses_rt', 'ditolak'],
            'diproses_rt' => ['diproses_rw', 'dalam_perbaikan', 'ditolak'],
            'diproses_rw' => ['dalam_perbaikan', 'ditolak'],
            'dalam_perbaikan' => ['selesai', 'diproses_rw'],
            'selesai' => [],
            'ditolak' => [],
        ];

        if ($from === $to) {
            return;
        }

        if (!in_array($to, $allowed[$from] ?? [], true)) {
            throw new RuntimeException('Perubahan status dari ' . $from . ' ke ' . $to . ' tidak diizinkan.');
        }
    }

    private function log(string $action, int $dataId, string $message, array $actor): void
    {
        $this->logs->insert([
            'user_id' => $actor['id'] ?? null,
            'aksi' => $action,
            'modul' => 'pengaduan',
            'data_id' => $dataId,
            'keterangan' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'CLI'), 0, 255),
        ]);
    }
}
