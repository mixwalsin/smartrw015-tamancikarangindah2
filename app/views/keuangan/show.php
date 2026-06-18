<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Detail Transaksi Kas</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?= formatDate((string) $transaction['date'], 'd F Y') ?></dd>
                <dt class="col-sm-3">Kas</dt><dd class="col-sm-9"><?= e(strtoupper((string) $transaction['kas_type'])) ?></dd>
                <dt class="col-sm-3">Jenis</dt><dd class="col-sm-9"><?= e((string) $transaction['transaction_type']) ?></dd>
                <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9"><?= e((string) ($transaction['category_name'] ?? '-')) ?></dd>
                <dt class="col-sm-3">Nominal</dt><dd class="col-sm-9 fw-semibold"><?= rupiah((float) $transaction['amount']) ?></dd>
                <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?= e((string) $transaction['status']) ?></dd>
                <dt class="col-sm-3">Deskripsi</dt><dd class="col-sm-9"><?= e((string) ($transaction['description'] ?? '-')) ?></dd>
                <dt class="col-sm-3">Bukti</dt><dd class="col-sm-9"><?php if (!empty($transaction['bukti_file'])): ?><a href="<?= url('storage/uploads/' . $transaction['bukti_file']) ?>" target="_blank">Lihat Bukti</a><?php else: ?>-<?php endif; ?></dd>
            </dl>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <a href="<?= url('keuangan/edit/' . $transaction['id']) ?>" class="btn btn-primary btn-sm">Edit</a>
            <a href="<?= url('keuangan') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </div>
</div>
