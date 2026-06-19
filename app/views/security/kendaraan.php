<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-car-front me-2 text-primary"></i>Manajemen Kendaraan</h4>
        <a href="<?= url('security') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">Input Kendaraan Masuk</div>
        <div class="card-body">
            <form method="POST" action="<?= url('security/kendaraan/store') ?>" class="row g-2">
                <?= csrfField() ?>
                <div class="col-md-3"><input type="text" name="no_plat" class="form-control" placeholder="No Plat" required></div>
                <div class="col-md-3"><input type="text" name="jenis_kendaraan" class="form-control" placeholder="Jenis"></div>
                <div class="col-md-3"><input type="text" name="merk" class="form-control" placeholder="Merk"></div>
                <div class="col-md-3"><input type="text" name="warna" class="form-control" placeholder="Warna"></div>
                <div class="col-md-4"><input type="text" name="nama_pemilik" class="form-control" placeholder="Nama Pemilik"></div>
                <div class="col-md-4"><input type="text" name="no_telepon_pemilik" class="form-control" placeholder="No Telepon"></div>
                <div class="col-md-4"><input type="text" name="lokasi_parkir" class="form-control" placeholder="Lokasi Parkir"></div>
                <div class="col-md-8"><input type="text" name="keperluan" class="form-control" placeholder="Keperluan"></div>
                <div class="col-md-4 d-grid"><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">Riwayat Kendaraan</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>No Plat</th>
                    <th>Pemilik</th>
                    <th>Status</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $row): ?>
                    <tr>
                        <td><code><?= e((string) ($row['no_plat'] ?? '')) ?></code></td>
                        <td><?= e((string) ($row['nama_pemilik'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['status'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['jam_masuk'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['jam_keluar'] ?? '-')) ?></td>
                        <td>
                            <?php if (($row['status'] ?? '') === 'parkir'): ?>
                                <form action="<?= url('security/kendaraan/checkout/' . (int) ($row['id'] ?? 0)) ?>" method="POST" class="d-inline">
                                    <?= csrfField() ?>
                                    <button class="btn btn-sm btn-outline-danger">Check-Out</button>
                                </form>
                            <?php else: ?>
                                <span class="badge text-bg-success">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data kendaraan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
