<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/KasService.php';

class KasCategoryController extends Controller
{
    private KasService $service;

    public function __construct()
    {
        $this->service = new KasService();
    }

    public function index(): void
    {
        $this->requireAuth();

        $this->view('keuangan/categories', [
            'title' => 'Kategori Kas - ' . APP_NAME,
            'categories' => $this->service->categories(),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan/categories');
        }

        try {
            $this->service->createCategory($_POST);
            setFlash('success', 'Kategori berhasil ditambahkan.');
        } catch (Throwable $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('keuangan/categories');
    }

    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan/categories');
        }

        $this->service->deleteCategory((int) $id);
        setFlash('success', 'Kategori berhasil dihapus.');
        $this->redirect('keuangan/categories');
    }
}
