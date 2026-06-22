<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/PengaduanService.php';

class ApiPengaduanController extends Controller
{
    private PengaduanService $service;

    public function __construct()
    {
        $this->service = new PengaduanService();
    }

    public function index(): void
    {
        $this->requireAuth();
        $actor = authUser() ?? [];
        $filters = [
            'status' => (string) $this->query('status', ''),
            'kategori_id' => (string) $this->query('kategori_id', ''),
        ];
        $page = (int) $this->query('page', 1);

        $dashboard = $this->service->dashboard($filters, $page, $actor);
        $this->json([
            'summary' => $dashboard['summary'],
            'data' => $dashboard['pagination']['data'],
            'pagination' => [
                'total' => $dashboard['pagination']['total'],
                'current_page' => $dashboard['pagination']['current_page'],
                'last_page' => $dashboard['pagination']['last_page'],
            ],
        ]);
    }
}
