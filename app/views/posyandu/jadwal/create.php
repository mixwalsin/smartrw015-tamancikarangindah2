<!-- Buat Jadwal Posyandu -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/jadwal') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Buat Jadwal Posyandu</h4>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('posyandu/jadwal/store') ?>" method="POST">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" required
                                       value="<?= e($_POST['tanggal'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control"
                                       value="<?= e($_POST['jam_mulai'] ?? '08:00') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control"
                                       value="<?= e($_POST['jam_selesai'] ?? '12:00') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control" required
                                       placeholder="Contoh: Posyandu Melati RW015"
                                       value="<?= e($_POST['lokasi'] ?? '') ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                       placeholder="Tema, agenda khusus, dll."
                                       value="<?= e($_POST['keterangan'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <?php foreach (['dijadwalkan' => 'Dijadwalkan', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $v => $l): ?>
                                        <option value="<?= $v ?>" <?= ($_POST['status'] ?? 'dijadwalkan') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan</button>
                            <a href="<?= url('posyandu/jadwal') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
