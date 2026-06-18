<!-- Edit Kartu Keluarga -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('kartukeluarga/show/' . $kk['id']) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Edit Kartu Keluarga</h4>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('kartukeluarga/update/' . $kk['id']) ?>" method="POST">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nomor KK</label>
                                <input type="text" class="form-control" value="<?= e($kk['no_kk']) ?>" readonly>
                                <div class="form-text">Nomor KK tidak dapat diubah.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">RT <span class="text-danger">*</span></label>
                                <select name="rt" id="rtSelect" class="form-select" required
                                        onchange="syncRtId(this)">
                                    <option value="">-- Pilih RT --</option>
                                    <?php
                                    $currentRt   = $_POST['rt']    ?? $kk['rt_text'];
                                    $currentRtId = $_POST['rt_id'] ?? $kk['rt_id'];
                                    foreach ($rtList as $kode => $rid):
                                    ?>
                                        <option value="<?= $kode ?>" data-id="<?= $rid ?>"
                                            <?= $currentRt === $kode ? 'selected' : '' ?>>
                                            RT <?= $kode ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="rt_id" id="rtId"
                                       value="<?= e($currentRtId) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">RW</label>
                                <input type="text" class="form-control" value="RW 015" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="3" required><?= e($_POST['alamat'] ?? $kk['alamat']) ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="<?= url('kartukeluarga/show/' . $kk['id']) ?>"
                               class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function syncRtId(sel) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('rtId').value = opt.dataset.id || '';
}
(function() {
    const sel = document.getElementById('rtSelect');
    if (sel.value) syncRtId(sel);
})();
</script>
