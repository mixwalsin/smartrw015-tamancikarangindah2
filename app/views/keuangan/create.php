<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Input Transaksi Kas RW/RT</div>
        <div class="card-body">
            <form method="post" action="<?= url('keuangan/store') ?>" enctype="multipart/form-data" class="row g-3">
                <?= csrfField() ?>
                <div class="col-md-3">
                    <label class="form-label">Kas</label>
                    <select name="kas_type" class="form-select" required>
                        <option value="rw">Kas RW</option>
                        <option value="rt">Kas RT</option>
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">RT (opsional)</label><input type="number" min="1" name="rt_id" class="form-control" placeholder="ID RT"></div>
                <div class="col-md-3">
                    <label class="form-label">Jenis</label>
                    <select name="transaction_type" class="form-select" required>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Tanggal</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?> (<?= e($category['kas_type']) ?>/<?= e($category['transaction_type']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Nominal</label><input type="number" min="1" step="0.01" name="amount" class="form-control" required></div>
                <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3" placeholder="Keterangan transaksi"></textarea></div>
                <div class="col-md-6"><label class="form-label">Bukti Transaksi</label><input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"></div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-success">Simpan</button>
                    <a href="<?= url('keuangan') ?>" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
