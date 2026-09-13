<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Departments</h4>
    <?php if (Auth::can('departments.manage')): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newDept"><i class="bi bi-plus-lg"></i> New Department</button>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Head</th><th>Headcount</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($departments as $d): ?>
                <tr>
                    <td><?= e($d['code']) ?></td>
                    <td><?= e($d['name']) ?></td>
                    <td><?= e($d['head_name'] ?? '—') ?></td>
                    <td><?= (int) $d['headcount'] ?></td>
                    <td><?= ((int) $d['is_active']) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                    <td class="text-end">
                        <?php if (Auth::can('departments.manage')): ?>
                            <form method="post" action="<?= url('/departments/delete') ?>" class="d-inline" onsubmit="return confirm('Archive this department?')">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-archive"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (Auth::can('departments.manage')): ?>
<div class="modal fade" id="newDept" tabindex="-1">
    <div class="modal-dialog"><form method="post" action="<?= url('/departments/store') ?>" class="modal-content">
        <?= CSRF::field() ?>
        <div class="modal-header"><h5 class="modal-title">New Department</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-2"><label class="form-label">Code *</label><input name="code" class="form-control" required maxlength="20"></div>
            <div class="mb-2"><label class="form-label">Name *</label><input name="name" class="form-control" required maxlength="150"></div>
            <div class="mb-2"><label class="form-label">Head Employee ID (optional)</label><input type="number" name="head_employee_id" class="form-control"></div>
            <div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" checked><label class="form-check-label">Active</label></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary" type="submit">Save</button></div>
    </form></div>
</div>
<?php endif; ?>