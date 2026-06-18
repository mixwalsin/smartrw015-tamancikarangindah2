<!-- Admin: Assign Role ke Pengguna -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Assign Role ke Pengguna</h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role Saat Ini</th>
                            <th>Status</th>
                            <th>Ubah Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= (int) $user['id'] ?></td>
                                <td class="fw-semibold"><?= e($user['name']) ?></td>
                                <td><?= e($user['username']) ?></td>
                                <td class="text-muted small"><?= e($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= e($user['role_name'] ?? $user['role_slug'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ((int) $user['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST"
                                          action="<?= url('admin/user-roles/assign/' . $user['id']) ?>"
                                          class="d-flex gap-2 align-items-center">
                                        <?= csrfField() ?>
                                        <select name="role_id" class="form-select form-select-sm" style="min-width:150px">
                                            <?php foreach ($roles as $role): ?>
                                                <?php $disabled = ($role['slug'] === 'super_admin' && !isSuperAdmin()) ? 'disabled' : ''; ?>
                                                <option value="<?= (int) $role['id'] ?>"
                                                        <?= (int)$user['role_id'] === (int)$role['id'] ? 'selected' : '' ?>
                                                        <?= $disabled ?>>
                                                    <?= e($role['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
