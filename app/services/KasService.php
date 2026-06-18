<?php

declare(strict_types=1);

require_once APP_PATH . '/repositories/KasTransactionRepository.php';
require_once APP_PATH . '/repositories/KasCategoryRepository.php';
require_once APP_PATH . '/repositories/KasBalanceRepository.php';
require_once APP_PATH . '/core/Model.php';

class KasService
{
    private KasTransactionRepository $transactions;
    private KasCategoryRepository $categories;
    private KasBalanceRepository $balances;
    private Model $dbModel;

    public function __construct()
    {
        $this->transactions = new KasTransactionRepository();
        $this->categories = new KasCategoryRepository();
        $this->balances = new KasBalanceRepository();
        $this->dbModel = new class extends Model {
            protected string $table = 'log_aktivitas';
        };
    }

    public function listTransactions(array $filters, int $page = 1): array
    {
        return $this->transactions->paginate($filters, $page);
    }

    public function getTransaction(int $id): array|false
    {
        return $this->transactions->find($id);
    }

    public function createTransaction(array $payload, array $files, array $actor): int
    {
        $data = $this->validateTransaction($payload);
        $data['created_by'] = (int) ($actor['id'] ?? 0);
        $data['status'] = $this->isAutoApproveRole($actor['role'] ?? '') ? 'approved' : 'pending';

        if (!empty($files['bukti_file']['name'])) {
            $data['bukti_file'] = uploadFile($files['bukti_file'], 'keuangan');
        }

        $id = $this->transactions->create($data);

        if ($data['status'] === 'approved') {
            $this->applyBalance((int) $id);
        }

        $this->logActivity($actor, 'create', 'kas_transactions', (int) $id, 'Menambahkan transaksi kas');
        $this->notifyLargeTransaction($data, (int) $id);

        return (int) $id;
    }

    public function updateTransaction(int $id, array $payload, array $files, array $actor): bool
    {
        $existing = $this->transactions->find($id);
        if (!$existing) {
            throw new RuntimeException('Transaksi tidak ditemukan.');
        }

        $this->assertCanModify($existing, $actor);
        $data = $this->validateTransaction($payload);

        if (!empty($files['bukti_file']['name'])) {
            $data['bukti_file'] = uploadFile($files['bukti_file'], 'keuangan');
        }

        $newStatus = $payload['status'] ?? $existing['status'];
        if (!in_array($newStatus, ['pending', 'approved', 'rejected'], true)) {
            $newStatus = $existing['status'];
        }
        $data['status'] = $newStatus;

        if ($existing['status'] === 'approved') {
            $this->rollbackBalance($existing);
        }

        $ok = $this->transactions->update($id, $data);

        $updated = $this->transactions->find($id);
        if ($updated && $updated['status'] === 'approved') {
            $this->applyBalance($id);
        }

        $this->logActivity($actor, 'update', 'kas_transactions', $id, 'Memperbarui transaksi kas');

        return $ok;
    }

    public function deleteTransaction(int $id, array $actor): bool
    {
        $existing = $this->transactions->find($id);
        if (!$existing) {
            return false;
        }

        $this->assertCanModify($existing, $actor);

        if ($existing['status'] === 'approved') {
            $this->rollbackBalance($existing);
        }

        $ok = $this->transactions->delete($id);
        $this->logActivity($actor, 'delete', 'kas_transactions', $id, 'Menghapus transaksi kas');

        return $ok;
    }

    public function approveOrReject(int $id, string $status, array $actor): bool
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            throw new RuntimeException('Status tidak valid.');
        }

        $existing = $this->transactions->find($id);
        if (!$existing) {
            throw new RuntimeException('Transaksi tidak ditemukan.');
        }

        if (!$this->isApproverRole($actor['role'] ?? '')) {
            throw new RuntimeException('Anda tidak memiliki izin approval.');
        }

        if ($existing['status'] === 'approved' && $status !== 'approved') {
            $this->rollbackBalance($existing);
        }

        $ok = $this->transactions->update($id, ['status' => $status]);

        if ($status === 'approved') {
            $this->applyBalance($id);
        }

        $this->logActivity($actor, $status === 'approved' ? 'approve' : 'reject', 'kas_transactions', $id, 'Mengubah status transaksi kas menjadi ' . $status);

        return $ok;
    }

    public function dashboardOverview(): array
    {
        $summary = $this->transactions->summaryThisMonth();
        $balances = $this->balances->listBalances();

        $rwBalance = 0.0;
        $rtBalance = 0.0;
        foreach ($balances as $balance) {
            if ($balance['kas_type'] === 'rw') {
                $rwBalance += (float) $balance['balance'];
            } else {
                $rtBalance += (float) $balance['balance'];
            }
        }

        return [
            'pemasukan' => (float) ($summary['pemasukan'] ?? 0),
            'pengeluaran' => (float) ($summary['pengeluaran'] ?? 0),
            'saldo_rw' => $rwBalance,
            'saldo_rt_total' => $rtBalance,
            'recent' => $this->transactions->recent(8),
        ];
    }

    public function getBalances(): array
    {
        return $this->balances->listBalances();
    }

    public function categories(?string $kasType = null, ?string $transactionType = null): array
    {
        return $this->categories->all($kasType, $transactionType);
    }

    public function createCategory(array $payload): int
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $kasType = (string) ($payload['kas_type'] ?? 'rw');
        $transactionType = (string) ($payload['transaction_type'] ?? 'pemasukan');

        if ($name === '') {
            throw new RuntimeException('Nama kategori wajib diisi.');
        }

        if (!in_array($kasType, ['rw', 'rt'], true) || !in_array($transactionType, ['pemasukan', 'pengeluaran'], true)) {
            throw new RuntimeException('Jenis kategori tidak valid.');
        }

        return $this->categories->create([
            'name' => $name,
            'slug' => slug($name),
            'kas_type' => $kasType,
            'transaction_type' => $transactionType,
            'description' => trim((string) ($payload['description'] ?? '')),
        ]);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categories->delete($id);
    }

    private function validateTransaction(array $payload): array
    {
        $kasType = (string) ($payload['kas_type'] ?? 'rw');
        $transactionType = (string) ($payload['transaction_type'] ?? 'pemasukan');
        $categoryId = (int) ($payload['category_id'] ?? 0);
        $amount = (float) ($payload['amount'] ?? 0);
        $date = (string) ($payload['date'] ?? '');
        $status = (string) ($payload['status'] ?? 'pending');
        $rtId = isset($payload['rt_id']) && $payload['rt_id'] !== '' ? (int) $payload['rt_id'] : null;

        if (!in_array($kasType, ['rw', 'rt'], true)) {
            throw new RuntimeException('Kas type tidak valid.');
        }

        if (!in_array($transactionType, ['pemasukan', 'pengeluaran'], true)) {
            throw new RuntimeException('Jenis transaksi tidak valid.');
        }

        if ($amount <= 0) {
            throw new RuntimeException('Nominal transaksi harus lebih besar dari 0.');
        }

        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new RuntimeException('Tanggal transaksi tidak valid.');
        }

        $category = $this->categories->find($categoryId);
        if (!$category) {
            throw new RuntimeException('Kategori tidak ditemukan.');
        }

        if ($category['kas_type'] !== $kasType || $category['transaction_type'] !== $transactionType) {
            throw new RuntimeException('Kategori tidak sesuai dengan jenis transaksi.');
        }

        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
        }

        return [
            'kas_type' => $kasType,
            'rt_id' => $kasType === 'rt' ? $rtId : null,
            'transaction_type' => $transactionType,
            'category_id' => $categoryId,
            'amount' => $amount,
            'description' => trim((string) ($payload['description'] ?? '')),
            'date' => $date,
            'user_id' => isset($payload['user_id']) && $payload['user_id'] !== '' ? (int) $payload['user_id'] : null,
            'status' => $status,
        ];
    }

    private function applyBalance(int $transactionId): void
    {
        $transaction = $this->transactions->find($transactionId);
        if (!$transaction || $transaction['status'] !== 'approved') {
            return;
        }

        $current = $this->balances->getBalance($transaction['kas_type'], $transaction['rt_id'] ? (int) $transaction['rt_id'] : null);
        $amount = (float) $transaction['amount'];
        $next = $transaction['transaction_type'] === 'pemasukan' ? $current + $amount : $current - $amount;

        if (KEUANGAN_PREVENT_NEGATIVE_BALANCE && $next < 0) {
            throw new RuntimeException('Saldo tidak mencukupi untuk transaksi pengeluaran.');
        }

        $this->balances->updateBalance($transaction['kas_type'], $transaction['rt_id'] ? (int) $transaction['rt_id'] : null, $next, $transactionId);
    }

    private function rollbackBalance(array $transaction): void
    {
        $current = $this->balances->getBalance($transaction['kas_type'], $transaction['rt_id'] ? (int) $transaction['rt_id'] : null);
        $amount = (float) $transaction['amount'];

        $next = $transaction['transaction_type'] === 'pemasukan' ? $current - $amount : $current + $amount;
        $this->balances->updateBalance($transaction['kas_type'], $transaction['rt_id'] ? (int) $transaction['rt_id'] : null, $next, (int) $transaction['id']);
    }

    private function assertCanModify(array $transaction, array $actor): void
    {
        $role = $actor['role'] ?? '';
        $isPrivileged = in_array($role, ['super_admin', 'admin', 'bendahara_rw', 'bendahara_rt', 'rw', 'rt'], true);
        if ($isPrivileged) {
            return;
        }

        if ((int) ($transaction['created_by'] ?? 0) !== (int) ($actor['id'] ?? 0)) {
            throw new RuntimeException('Anda tidak berhak mengubah transaksi ini.');
        }
    }

    private function isApproverRole(string $role): bool
    {
        return in_array($role, ['super_admin', 'admin', 'bendahara_rw', 'bendahara_rt', 'rw'], true);
    }

    private function isAutoApproveRole(string $role): bool
    {
        return $this->isApproverRole($role);
    }

    private function notifyLargeTransaction(array $data, int $transactionId): void
    {
        if (($data['amount'] ?? 0) < KEUANGAN_NOTIF_THRESHOLD) {
            return;
        }

        $this->dbModel->execute(
            'INSERT INTO notifikasi (user_id, judul, pesan, tipe, is_read, created_at) VALUES (?, ?, ?, ?, 0, ?)',
            [
                null,
                'Transaksi Besar Terdeteksi',
                'Transaksi kas ID #' . $transactionId . ' melebihi threshold notifikasi.',
                'peringatan',
                date('Y-m-d H:i:s'),
            ]
        );
    }

    private function logActivity(array $actor, string $action, string $module, int $dataId, string $description): void
    {
        $this->dbModel->execute(
            'INSERT INTO log_aktivitas (user_id, aksi, modul, data_id, keterangan, ip_address, user_agent, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                (int) ($actor['id'] ?? 0) ?: null,
                $action,
                $module,
                $dataId,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
                date('Y-m-d H:i:s'),
            ]
        );
    }
}
