<?php

declare(strict_types=1);

require_once APP_PATH . '/repositories/PengaduanRepository.php';

class ReportService
{
    private PengaduanRepository $repository;

    public function __construct()
    {
        $this->repository = new PengaduanRepository();
    }

    public function dashboard(array $actor): array
    {
        return [
            'summary' => $this->repository->summary($actor),
            'categories' => $this->repository->categoryBreakdown($actor),
            'trend' => $this->repository->trendByMonth($actor),
        ];
    }

    public function rowsForExport(array $actor): array
    {
        return $this->repository->paginate([], 1, $actor, 5000)['data'];
    }
}
