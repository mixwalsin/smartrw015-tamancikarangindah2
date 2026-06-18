<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="<?= url('pengaduan') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-0">Buat Pengaduan Baru</h4>
                    <div class="text-muted small">Unggah bukti foto, tentukan kategori, dan kirim laporan untuk diverifikasi RT/RW.</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('pengaduan/store') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" class="form-select" required>
                                    <option value="">-- Pilih kategori --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prioritas</label>
                                <select name="prioritas" class="form-select">
                                    <?php foreach ($priorities as $value => $label): ?>
                                        <option value="<?= e($value) ?>" <?= $value === 'sedang' ? 'selected' : '' ?>><?= e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Judul Pengaduan <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required maxlength="150" placeholder="Contoh: Lampu jalan mati di RT 003">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" maxlength="255" placeholder="Contoh: Gang Melati dekat pos ronda RT003">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control" rows="5" required placeholder="Jelaskan masalah, kronologi, dampak, dan kebutuhan tindak lanjut."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Foto</label>
                                <input type="file" name="foto[]" class="form-control" accept=".jpg,.jpeg,.png,.gif" multiple>
                                <div class="form-text">Maksimal <?= PENGADUAN_MAX_PHOTOS ?> foto. Format JPG, PNG, GIF. Batas ukuran mengikuti konfigurasi aplikasi.</div>
                            </div>
                            <div class="col-12">
                                <div id="previewGrid" class="row g-2"></div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Kirim Pengaduan</button>
                            <a href="<?= url('pengaduan') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(() => {
    const input = document.querySelector('input[name="foto[]"]');
    const preview = document.getElementById('previewGrid');
    if (!input || !preview) return;
    input.addEventListener('change', () => {
        preview.innerHTML = '';
        [...input.files].forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const col = document.createElement('div');
            col.className = 'col-md-3 col-6';
            const card = document.createElement('div');
            card.className = 'border rounded p-2 h-100';
            const img = document.createElement('img');
            img.className = 'img-fluid rounded';
            img.alt = file.name;
            img.src = URL.createObjectURL(file);
            const text = document.createElement('div');
            text.className = 'small text-muted mt-2';
            text.textContent = file.name;
            card.appendChild(img);
            card.appendChild(text);
            col.appendChild(card);
            preview.appendChild(col);
        });
    });
})();
</script>
