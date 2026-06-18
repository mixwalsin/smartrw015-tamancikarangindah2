<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>Modul Keuangan RW Digital</h4>
        <div class="d-flex gap-2">
            <a href="<?= url('keuangan/create') ?>" class="btn btn-success btn-sm"><i class="bi bi-plus-circle me-1"></i>Input Transaksi</a>
            <a href="<?= url('keuangan/categories') ?>" class="btn btn-outline-primary btn-sm">Kategori</a>
            <a href="<?= url('keuangan/balance') ?>" class="btn btn-outline-dark btn-sm">Saldo</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Saldo Kas RW</div><div class="fw-bold fs-5 text-primary"><?= rupiah((float) ($overview['saldo_rw'] ?? 0)) ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total Saldo Kas RT</div><div class="fw-bold fs-5 text-info"><?= rupiah((float) ($overview['saldo_rt_total'] ?? 0)) ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Pemasukan Bulan Ini</div><div class="fw-bold fs-5 text-success"><?= rupiah((float) ($overview['pemasukan'] ?? 0)) ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Pengeluaran Bulan Ini</div><div class="fw-bold fs-5 text-danger"><?= rupiah((float) ($overview['pengeluaran'] ?? 0)) ?></div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" class="row g-2">
                <div class="col-md-2">
                    <select name="kas_type" class="form-select form-select-sm">
                        <option value="">Semua Kas</option>
                        <option value="rw" <?= ($filters['kas_type'] ?? '') === 'rw' ? 'selected' : '' ?>>Kas RW</option>
                        <option value="rt" <?= ($filters['kas_type'] ?? '') === 'rt' ? 'selected' : '' ?>>Kas RT</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="transaction_type" class="form-select form-select-sm">
                        <option value="">Semua Jenis</option>
                        <option value="pemasukan" <?= ($filters['transaction_type'] ?? '') === 'pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                        <option value="pengeluaran" <?= ($filters['transaction_type'] ?? '') === 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="<?= e((string) ($filters['date_from'] ?? '')) ?>"></div>
                <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="<?= e((string) ($filters['date_to'] ?? '')) ?>"></div>
                <div class="col-md-2"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari deskripsi/kategori" value="<?= e((string) ($filters['search'] ?? '')) ?>"></div>
                <div class="col-12 text-end"><button class="btn btn-primary btn-sm">Filter</button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Daftar Transaksi</span>
            <div class="d-flex gap-2">
                <a href="<?= url('keuangan/report/monthly') ?>" class="btn btn-outline-success btn-sm">Laporan Bulanan</a>
                <a href="<?= url('keuangan/report/yearly') ?>" class="btn btn-outline-secondary btn-sm">Laporan Tahunan</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>Tanggal</th><th>Kas</th><th>Jenis</th><th>Kategori</th><th>Nominal</th><th>Status</th><th>Bukti</th><th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (($transactions['data'] ?? []) as $row): ?>
                    <tr>
                        <td><?= formatDate((string) $row['date'], 'd/m/Y') ?></td>
                        <td><?= e(strtoupper((string) $row['kas_type'])) ?><?= !empty($row['rt_kode']) ? ' - RT ' . e((string) $row['rt_kode']) : '' ?></td>
                        <td><span class="badge text-bg-<?= $row['transaction_type'] === 'pemasukan' ? 'success' : 'danger' ?>"><?= e((string) $row['transaction_type']) ?></span></td>
                        <td><?= e((string) ($row['category_name'] ?? '-')) ?></td>
                        <td class="fw-semibold"><?= rupiah((float) $row['amount']) ?></td>
                        <td><span class="badge text-bg-<?= $row['status'] === 'approved' ? 'success' : ($row['status'] === 'rejected' ? 'danger' : 'warning') ?>"><?= e((string) $row['status']) ?></span></td>
                        <td>
                            <?php if (!empty($row['bukti_file'])): ?>
                                <a href="<?= url('storage/uploads/' . $row['bukti_file']) ?>" target="_blank">Lihat</a>
                            <?php else: ?>-
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a class="btn btn-outline-primary btn-sm" href="<?= url('keuangan/edit/' . $row['id']) ?>">Edit</a>
                                <form method="post" action="<?= url('keuangan/delete/' . $row['id']) ?>" onsubmit="return confirm('Hapus transaksi ini?')">
                                    <?= csrfField() ?>
                                    <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                                <?php if (($row['status'] ?? '') === 'pending'): ?>
                                    <form method="post" action="<?= url('keuangan/approve/' . $row['id']) ?>"><?= csrfField() ?><button class="btn btn-outline-success btn-sm">Approve</button></form>
                                    <form method="post" action="<?= url('keuangan/reject/' . $row['id']) ?>"><?= csrfField() ?><button class="btn btn-outline-warning btn-sm">Reject</button></form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($transactions['data'])): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white"><?= paginate($transactions, 'keuangan') ?></div>
    </div>
</div>
