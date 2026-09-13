<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Employees</h4>
    <?php if (Auth::can('employees.create')): ?>
    <a class="btn btn-primary" href="<?= url('/employees/create') ?>"><i class="bi bi-plus-lg"></i> New Employee</a>
    <?php endif; ?>
</div>

<form class="card shadow-sm border-0 mb-3">
    <div class="card-body row g-2">
        <div class="col-md-4"><input class="form-control" name="q" placeholder="Search name / code / email" value="<?= e($filters['q']) ?>"></div>
        <div class="col-md-3">
            <select name="department_id" class="form-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= (int) $d['id'] ?>" <?= (int) $filters['department_id'] === (int) $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <?php foreach (['Probationary','Regular','Contractual','Project-Based','Part-Time','Casual','Separated'] as $s): ?>
                    <option value="<?= e($s) ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Code</th><th>Name</th><th>Department</th><th>Position</th>
                    <th>Status</th><th>Salary</th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $e): ?>
                <tr>
                    <td><?= e($e['employee_code']) ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($e['first_name'] . ' ' . $e['last_name']) ?></div>
                        <small class="text-muted"><?= e($e['email'] ?? '') ?></small>
                    </td>
                    <td><?= e($e['department_name'] ?? '—') ?></td>
                    <td><?= e($e['position_name'] ?? '—') ?></td>
                    <td>
                        <?php
                        $badge = match ($e['employment_status']) {
                            'Regular' => 'success',
                            'Probationary' => 'warning',
                            'Separated' => 'secondary',
                            default => 'info',
                        };
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= e($e['employment_status']) ?></span>
                    </td>
                    <td>₱<?= number_format((float) $e['basic_salary'], 2) ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('/employees/view?id=' . $e['id']) ?>"><i class="bi bi-eye"></i></a>
                        <?php if (Auth::can('employees.edit')): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= url('/employees/edit?id=' . $e['id']) ?>"><i class="bi bi-pencil"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$employees): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">No employees found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php View::partial('pagination', ['pagination' => $pagination, 'baseUrl' => '/employees']); ?>
</div>