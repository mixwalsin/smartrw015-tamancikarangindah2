<?php

declare(strict_types=1);

class NotifikasiController extends Controller
{
    private NotificationModel $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $rows = $this->model->listForUser((int) (authUser()['id'] ?? 0));
        $pagination = [
            'data' => $rows,
            'total' => count($rows),
            'per_page' => count($rows) ?: 1,
            'current_page' => 1,
            'last_page' => 1,
            'keyword' => '',
        ];

        $this->renderModuleIndex([
            'title' => 'Notifikasi',
            'icon' => 'bi-bell',
            'routeBase' => 'notifikasi',
            'columns' => [
                ['key' => 'judul', 'label' => 'Judul'],
                ['key' => 'pesan', 'label' => 'Pesan'],
                ['key' => 'tipe', 'label' => 'Tipe'],
                ['key' => 'created_at', 'label' => 'Dibuat'],
            ],
            'pagination' => $pagination,
            'canCreate' => false,
            'canEdit' => false,
            'canDelete' => false,
            'canShow' => false,
            'subtitle' => 'Broadcast sistem dan notifikasi pribadi pengguna.',
            'extraButtons' => [],
        ]);
    }

    public function markRead(string $id): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('notifikasi');
        }

        $this->model->update((int) $id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);
        setFlash('success', 'Notifikasi ditandai sebagai dibaca.');
        $this->redirect('notifikasi');
    }
}
