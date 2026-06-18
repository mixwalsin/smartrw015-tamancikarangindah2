<!-- Edit Jadwal Posyandu -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/jadwal') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Edit Jadwal Posyandu</h4>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('posyandu/jadwal/update/' . $jadwal['id']) ?>" method="POST">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" required
                                       value="<?= e($_POST['tanggal'] ?? $jadwal['tanggal']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control"
                                       value="<?= e($_POST['jam_mulai'] ?? $jadwal['jam_mulai']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control"
                                       value="<?= e($_POST['jam_selesai'] ?? $jadwal['jam_selesai']) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control" required
                                       value="<?= e($_POST['lokasi'] ?? $jadwal['lokasi']) ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                       value="<?= e($_POST['keterangan'] ?? $jadwal['keterangan'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <?php foreach (['dijadwalkan' => 'Dijadwalkan', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $v => $l): ?>
                                        <option value="<?= $v ?>"
                                            <?= ($_POST['status'] ?? $jadwal['status']) === $v ? 'selected' : '' ?>>
                                            <?= $l ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Perbarui</button>
                            <a href="<?= url('posyandu/jadwal') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
