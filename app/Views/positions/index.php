<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Positions</h4>
    <?php if (Auth::can('positions.manage')): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPos"><i class="bi bi-plus-lg"></i> New Position</button>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Department</th><th>Salary Range</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($positions as $p): ?>
                <tr>
                    <td><?= e($p['code']) ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['department_name'] ?? '—') ?></td>
                    <td>
                        <?= $p['salary_min'] !== null ? '₱' . number_format((float) $p['salary_min'], 2) : '—' ?>
                        <?= $p['salary_max'] !== null ? ' - ₱' . number_format((float) $p['salary_max'], 2) : '' ?>
                    </td>
                    <td><?= ((int) $p['is_active']) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                    <td class="text-end">
                        <?php if (Auth::can('positions.manage')): ?>
                            <form method="post" action="<?= url('/positions/delete') ?>" class="d-inline" onsubmit="return confirm('Archive this position?')">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
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

<?php if (Auth::can('positions.manage')): ?>
<div class="modal fade" id="newPos" tabindex="-1">
    <div class="modal-dialog"><form method="post" action="<?= url('/positions/store') ?>" class="modal-content">
        <?= CSRF::field() ?>
        <div class="modal-header"><h5 class="modal-title">New Position</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-2"><label class="form-label">Code *</label><input name="code" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">—</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int) $d['id'] ?>"><?= e($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row g-2">
                <div class="col"><label class="form-label">Salary Min</label><input type="number" step="0.01" name="salary_min" class="form-control"></div>
                <div class="col"><label class="form-label">Salary Max</label><input type="number" step="0.01" name="salary_max" class="form-control"></div>
            </div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary" type="submit">Save</button></div>
    </form></div>
</div>
<?php endif; ?>