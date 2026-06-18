<?php

declare(strict_types=1);

require_once APP_PATH . '/models/PengaduanModel.php';
require_once APP_PATH . '/models/PengaduanFotoModel.php';
require_once APP_PATH . '/models/PengaduanKomentarModel.php';
require_once APP_PATH . '/models/PengaduanDisposisiRtModel.php';
require_once APP_PATH . '/models/PengaduanDisposisiRwModel.php';
require_once APP_PATH . '/models/PengaduanStatusHistoryModel.php';

class PengaduanRepository
{
    public function __construct(
        private readonly PengaduanModel $pengaduanModel = new PengaduanModel(),
        private readonly PengaduanFotoModel $fotoModel = new PengaduanFotoModel(),
        private readonly PengaduanKomentarModel $komentarModel = new PengaduanKomentarModel(),
        private readonly PengaduanDisposisiRtModel $disposisiRtModel = new PengaduanDisposisiRtModel(),
        private readonly PengaduanDisposisiRwModel $disposisiRwModel = new PengaduanDisposisiRwModel(),
        private readonly PengaduanStatusHistoryModel $historyModel = new PengaduanStatusHistoryModel(),
    ) {
    }

    public function paginate(array $filters, int $page, array $actor, int $perPage = PAGINATION_LIMIT): array
    {
        return $this->pengaduanModel->paginateWithRelations($filters, $page, $perPage, $actor);
    }

    public function create(array $data): int
    {
        return (int) $this->pengaduanModel->insert($data);
    }

    public function findDetail(int $id): array|false
    {
        $pengaduan = $this->pengaduanModel->findDetail($id);
        if (!$pengaduan) {
            return false;
        }

        $pengaduan['fotos'] = $this->fotoModel->getByPengaduan($id);
        $pengaduan['komentars'] = $this->komentarModel->getByPengaduan($id);
        $pengaduan['disposisi_rt'] = $this->disposisiRtModel->findLatestByPengaduan($id);
        $pengaduan['disposisi_rw'] = $this->disposisiRwModel->findLatestByPengaduan($id);
        $pengaduan['status_history'] = $this->historyModel->getByPengaduan($id);

        return $pengaduan;
    }

    public function updateStatus(int $id, string $status, ?string $note = null, ?string $rejectionReason = null): bool
    {
        return $this->pengaduanModel->updateStatusRecord($id, $status, $note, $rejectionReason);
    }

    public function addHistory(array $data): int
    {
        return (int) $this->historyModel->insert($data);
    }

    public function addPhoto(array $data): int
    {
        return (int) $this->fotoModel->insert($data);
    }

    public function removePhoto(int $id): bool
    {
        return $this->fotoModel->delete($id);
    }

    public function findPhoto(int $id): array|false
    {
        return $this->fotoModel->find($id);
    }

    public function addComment(array $data): int
    {
        return (int) $this->komentarModel->insert($data);
    }

    public function updateComment(int $id, array $data): bool
    {
        return $this->komentarModel->update($id, $data);
    }

    public function findComment(int $id): array|false
    {
        return $this->komentarModel->find($id);
    }

    public function deleteComment(int $id): bool
    {
        return $this->komentarModel->delete($id);
    }

    public function addRtDisposition(array $data): int
    {
        return (int) $this->disposisiRtModel->insert($data);
    }

    public function addRwDisposition(array $data): int
    {
        return (int) $this->disposisiRwModel->insert($data);
    }


    public function nextTicketSequence(string $period): int
    {
        return $this->pengaduanModel->nextTicketSequence($period);
    }

    public function ticketExists(string $ticket): bool
    {
        return $this->pengaduanModel->ticketExists($ticket);
    }

    public function summary(array $actor): array
    {
        return $this->pengaduanModel->getSummary($actor);
    }

    public function categoryBreakdown(array $actor): array
    {
        return $this->pengaduanModel->getCategoryBreakdown($actor);
    }

    public function trendByMonth(array $actor): array
    {
        return $this->pengaduanModel->getTrendByMonth($actor);
    }
}
