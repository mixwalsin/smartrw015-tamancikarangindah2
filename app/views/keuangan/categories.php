<div class="container-fluid">
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Tambah Kategori</div>
                <div class="card-body">
                    <form method="post" action="<?= url('keuangan/categories/store') ?>" class="row g-3">
                        <?= csrfField() ?>
                        <div class="col-12"><label class="form-label">Nama</label><input name="name" class="form-control" required></div>
                        <div class="col-6"><label class="form-label">Kas</label><select name="kas_type" class="form-select"><option value="rw">RW</option><option value="rt">RT</option></select></div>
                        <div class="col-6"><label class="form-label">Jenis</label><select name="transaction_type" class="form-select"><option value="pemasukan">Pemasukan</option><option value="pengeluaran">Pengeluaran</option></select></div>
                        <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                        <div class="col-12"><button class="btn btn-success">Simpan</button></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Daftar Kategori</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>Nama</th><th>Kas</th><th>Jenis</th><th>Deskripsi</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= e((string) $category['name']) ?></td>
                                <td><?= e(strtoupper((string) $category['kas_type'])) ?></td>
                                <td><?= e((string) $category['transaction_type']) ?></td>
                                <td><?= e((string) ($category['description'] ?? '-')) ?></td>
                                <td>
                                    <form method="post" action="<?= url('keuangan/categories/delete/' . $category['id']) ?>" onsubmit="return confirm('Hapus kategori ini?')">
                                        <?= csrfField() ?>
                                        <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
