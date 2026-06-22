<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Edit Transaksi Kas</div>
        <div class="card-body">
            <form method="post" action="<?= url('keuangan/update/' . $transaction['id']) ?>" enctype="multipart/form-data" class="row g-3">
                <?= csrfField() ?>
                <div class="col-md-3">
                    <label class="form-label">Kas</label>
                    <select name="kas_type" class="form-select" required>
                        <option value="rw" <?= $transaction['kas_type'] === 'rw' ? 'selected' : '' ?>>Kas RW</option>
                        <option value="rt" <?= $transaction['kas_type'] === 'rt' ? 'selected' : '' ?>>Kas RT</option>
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">RT (opsional)</label><input type="number" min="1" name="rt_id" class="form-control" value="<?= e((string) ($transaction['rt_id'] ?? '')) ?>"></div>
                <div class="col-md-3">
                    <label class="form-label">Jenis</label>
                    <select name="transaction_type" class="form-select" required>
                        <option value="pemasukan" <?= $transaction['transaction_type'] === 'pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                        <option value="pengeluaran" <?= $transaction['transaction_type'] === 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Tanggal</label><input type="date" name="date" class="form-control" value="<?= e((string) $transaction['date']) ?>" required></div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= (int) $transaction['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?> (<?= e($category['kas_type']) ?>/<?= e($category['transaction_type']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Nominal</label><input type="number" min="1" step="0.01" name="amount" class="form-control" value="<?= e((string) $transaction['amount']) ?>" required></div>
                <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3"><?= e((string) ($transaction['description'] ?? '')) ?></textarea></div>
                <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="pending" <?= $transaction['status'] === 'pending' ? 'selected' : '' ?>>Pending</option><option value="approved" <?= $transaction['status'] === 'approved' ? 'selected' : '' ?>>Approved</option><option value="rejected" <?= $transaction['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option></select></div>
                <div class="col-md-8"><label class="form-label">Bukti Baru (opsional)</label><input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"></div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">Simpan Perubahan</button>
                    <a href="<?= url('keuangan') ?>" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
