<div class="container">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Profil Pengguna</div>
                <div class="card-body">
                    <form method="POST" action="<?= url('profil/update') ?>">
                        <?= csrfField() ?>
                        <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="name" class="form-control" value="<?= e((string) ($user['name'] ?? authUser()['name'] ?? '')) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Username</label><input type="text" class="form-control" value="<?= e((string) ($user['username'] ?? authUser()['username'] ?? '')) ?>" disabled></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e((string) ($user['email'] ?? authUser()['email'] ?? '')) ?>" required></div>
                        <button class="btn btn-primary">Simpan Profil</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Ubah Password</div>
                <div class="card-body">
                    <form method="POST" action="<?= url('profil/password') ?>">
                        <?= csrfField() ?>
                        <div class="mb-3"><label class="form-label">Password Lama</label><input type="password" name="current_password" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Password Baru</label><input type="password" name="password" class="form-control" minlength="8" required></div>
                        <div class="mb-3"><label class="form-label">Konfirmasi Password Baru</label><input type="password" name="password_confirmation" class="form-control" minlength="8" required></div>
                        <button class="btn btn-outline-primary">Perbarui Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
