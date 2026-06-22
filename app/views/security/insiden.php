<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2 text-primary"></i>Laporan Insiden</h4>
        <a href="<?= url('security') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">Laporkan Insiden</div>
        <div class="card-body">
            <form method="POST" action="<?= url('security/insiden/store') ?>" class="row g-2">
                <?= csrfField() ?>
                <div class="col-md-3">
                    <select name="tipe_insiden" class="form-select">
                        <option value="kehilangan">Kehilangan</option>
                        <option value="keributan">Keributan</option>
                        <option value="kejadian_lain" selected>Kejadian Lain</option>
                    </select>
                </div>
                <div class="col-md-4"><input type="text" name="lokasi" class="form-control" placeholder="Lokasi"></div>
                <div class="col-md-5"><input type="text" name="nama_pelapor" class="form-control" placeholder="Nama Pelapor"></div>
                <div class="col-md-12"><textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi insiden" required></textarea></div>
                <div class="col-md-9"><input type="text" name="no_telepon_pelapor" class="form-control" placeholder="No Telepon Pelapor"></div>
                <div class="col-md-3 d-grid"><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">Daftar Insiden</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                    <th>Update Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $row): ?>
                    <tr>
                        <td><?= e((string) ($row['tanggal_insiden'] ?? '-')) ?> <?= e((string) ($row['jam_insiden'] ?? '')) ?></td>
                        <td><?= e((string) ($row['tipe_insiden'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['lokasi'] ?? '-')) ?></td>
                        <td><span class="badge text-bg-secondary"><?= e((string) ($row['status'] ?? 'baru')) ?></span></td>
                        <td><?= e((string) ($row['deskripsi'] ?? '-')) ?></td>
                        <td>
                            <form method="POST" action="<?= url('security/insiden/update-status/' . (int) ($row['id'] ?? 0)) ?>" class="d-flex gap-1">
                                <?= csrfField() ?>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="baru" <?= ($row['status'] ?? '') === 'baru' ? 'selected' : '' ?>>Baru</option>
                                    <option value="diproses" <?= ($row['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="selesai" <?= ($row['status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                                <button class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data insiden.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
