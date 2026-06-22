<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Manajemen Tamu</h4>
        <a href="<?= url('security') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">Check-In Tamu</div>
        <div class="card-body">
            <form method="POST" action="<?= url('security/tamu/store') ?>" class="row g-2">
                <?= csrfField() ?>
                <div class="col-md-3"><input type="text" name="nama" class="form-control" placeholder="Nama" required></div>
                <div class="col-md-3"><input type="text" name="no_identitas" class="form-control" placeholder="No Identitas" required></div>
                <div class="col-md-2"><input type="text" name="no_telepon" class="form-control" placeholder="No Telepon"></div>
                <div class="col-md-2"><input type="text" name="rt" class="form-control" placeholder="RT"></div>
                <div class="col-md-2"><input type="text" name="no_rumah" class="form-control" placeholder="No Rumah"></div>
                <div class="col-md-6"><input type="text" name="alamat_asal" class="form-control" placeholder="Alamat Asal"></div>
                <div class="col-md-6"><input type="text" name="keperluan" class="form-control" placeholder="Keperluan"></div>
                <div class="col-md-4"><input type="text" name="nama_penerima" class="form-control" placeholder="Nama Penerima"></div>
                <div class="col-md-4"><input type="text" name="catatan" class="form-control" placeholder="Catatan"></div>
                <div class="col-md-4 d-grid"><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">Daftar Tamu Hari Ini</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Identitas</th>
                    <th>RT/RW</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $row): ?>
                    <tr>
                        <td><?= e((string) ($row['nama'] ?? '')) ?></td>
                        <td><?= e((string) ($row['no_identitas'] ?? '')) ?></td>
                        <td>RT <?= e((string) ($row['rt'] ?? '-')) ?>/RW <?= e((string) ($row['rw'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['jam_masuk'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['jam_keluar'] ?? '-')) ?></td>
                        <td>
                            <?php if (empty($row['jam_keluar'])): ?>
                                <form action="<?= url('security/tamu/checkout/' . (int) ($row['id'] ?? 0)) ?>" method="POST" class="d-inline">
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
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data tamu.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
