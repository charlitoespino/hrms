<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">User Accounts</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newUser"><i class="bi bi-plus-lg"></i> New User</button>
</div>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Email</th><th>Name</th><th>Role</th><th>Employee</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['full_name']) ?></td>
                <td><span class="badge bg-primary"><?= e($u['role_name']) ?></span></td>
                <td><?= e($u['employee_code'] ?? '—') ?></td>
                <td><?= ((int) $u['is_active']) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary btn-toggle" data-id="<?= (int) $u['id'] ?>">
                        <?= ((int) $u['is_active']) ? 'Deactivate' : 'Activate' ?>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>

<div class="modal fade" id="newUser" tabindex="-1">
    <div class="modal-dialog"><form method="post" action="<?= url('/users/store') ?>" class="modal-content">
        <?= CSRF::field() ?>
        <div class="modal-header"><h5 class="modal-title">New User</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-2"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Full Name *</label><input name="full_name" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required minlength="8"></div>
            <div class="mb-2"><label class="form-label">Role *</label>
                <select name="role_id" class="form-select" required>
                    <option value="">—</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int) $r['id'] ?>"><?= e($r['display_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2"><label class="form-label">Linked Employee (optional)</label>
                <select name="employee_id" class="form-select">
                    <option value="">—</option>
                    <?php foreach ($employees as $e): ?>
                        <option value="<?= (int) $e['id'] ?>"><?= e($e['employee_code'] . ' — ' . $e['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary" type="submit">Create</button></div>
    </form></div>
</div>