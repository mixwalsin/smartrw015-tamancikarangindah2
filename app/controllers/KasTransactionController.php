<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/KasService.php';

class KasTransactionController extends Controller
{
    private KasService $service;

    public function __construct()
    {
        $this->service = new KasService();
    }

    public function index(): void
    {
        $this->requireAuth();

        $filters = [
            'kas_type' => $this->query('kas_type', ''),
            'transaction_type' => $this->query('transaction_type', ''),
            'category_id' => $this->query('category_id', ''),
            'status' => $this->query('status', ''),
            'date_from' => $this->query('date_from', ''),
            'date_to' => $this->query('date_to', ''),
            'search' => $this->query('search', ''),
            'rt_id' => $this->query('rt_id', ''),
        ];

        $page = (int) $this->query('page', 1);
        $transactions = $this->service->listTransactions($filters, $page);

        $this->view('keuangan/index', [
            'title' => 'Modul Keuangan - ' . APP_NAME,
            'overview' => $this->service->dashboardOverview(),
            'transactions' => $transactions,
            'filters' => $filters,
            'categories' => $this->service->categories(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->view('keuangan/create', [
            'title' => 'Input Transaksi Kas - ' . APP_NAME,
            'categories' => $this->service->categories(),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan/create');
        }

        try {
            $this->service->createTransaction($_POST, $_FILES, authUser() ?? []);
            setFlash('success', 'Transaksi berhasil disimpan.');
        } catch (Throwable $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('keuangan/create');
        }

        $this->redirect('keuangan');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();

        $transaction = $this->service->getTransaction((int) $id);
        if (!$transaction) {
            setFlash('error', 'Transaksi tidak ditemukan.');
            $this->redirect('keuangan');
        }

        $this->view('keuangan/edit', [
            'title' => 'Edit Transaksi Kas - ' . APP_NAME,
            'transaction' => $transaction,
            'categories' => $this->service->categories(),
        ]);
    }

    public function show(string $id): void
    {
        $this->requireAuth();

        $transaction = $this->service->getTransaction((int) $id);
        if (!$transaction) {
            setFlash('error', 'Transaksi tidak ditemukan.');
            $this->redirect('keuangan');
        }

        $this->view('keuangan/show', [
            'title' => 'Detail Transaksi Kas - ' . APP_NAME,
            'transaction' => $transaction,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan/edit/' . $id);
        }

        try {
            $this->service->updateTransaction((int) $id, $_POST, $_FILES, authUser() ?? []);
            setFlash('success', 'Transaksi berhasil diperbarui.');
        } catch (Throwable $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('keuangan/edit/' . $id);
        }

        $this->redirect('keuangan');
    }

    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan');
        }

        try {
            $this->service->deleteTransaction((int) $id, authUser() ?? []);
            setFlash('success', 'Transaksi berhasil dihapus.');
        } catch (Throwable $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('keuangan');
    }

    public function approve(string $id): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan');
        }

        try {
            $this->service->approveOrReject((int) $id, 'approved', authUser() ?? []);
            setFlash('success', 'Transaksi berhasil disetujui.');
        } catch (Throwable $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('keuangan');
    }

    public function reject(string $id): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan');
        }

        try {
            $this->service->approveOrReject((int) $id, 'rejected', authUser() ?? []);
            setFlash('success', 'Transaksi berhasil ditolak.');
        } catch (Throwable $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('keuangan');
    }
}
