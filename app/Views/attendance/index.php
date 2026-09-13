<h4 class="mb-3">Attendance</h4>

<div class="row g-2 mb-3">
    <?php foreach ($summary as $status => $count): ?>
        <div class="col-6 col-md-3 col-lg">
            <div class="card border-0 shadow-sm"><div class="card-body py-2">
                <div class="text-muted small"><?= e($status) ?></div>
                <div class="fs-5 fw-semibold"><?= (int) $count ?></div>
            </div></div>
        </div>
    <?php endforeach; ?>
</div>

<form class="card shadow-sm border-0 mb-3"><div class="card-body row g-2">
    <div class="col-md-2"><input type="date" name="from" class="form-control" value="<?= e($filters['from']) ?>"></div>
    <div class="col-md-2"><input type="date" name="to" class="form-control" value="<?= e($filters['to']) ?>"></div>
    <div class="col-md-3"><select name="department_id" class="form-select">
        <option value="">All Departments</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= (int) $d['id'] ?>" <?= (int) $filters['department_id'] === (int) $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><select name="status" class="form-select">
        <option value="">All Status</option>
        <?php foreach (['Present','Late','Absent','On Leave','Holiday','Rest Day','Official Business','Work From Home','Half Day'] as $s): ?>
            <option <?= $filters['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
</div></form>

<div class="card shadow-sm border-0">
    <div class="table-responsive"><table class="table table-hover mb-0">
        <thead class="table-light"><tr>
            <th>Date</th><th>Code</th><th>Name</th><th>Dept</th><th>In</th><th>Out</th>
            <th>Hours</th><th>Late (m)</th><th>Undertime (m)</th><th>OT (m)</th><th>Status</th>
        </tr></thead>
        <tbody>
        <?php foreach ($records as $r): ?>
            <tr>
                <td><?= e($r['attendance_date']) ?></td>
                <td><?= e($r['employee_code']) ?></td>
                <td><?= e($r['employee_name']) ?></td>
                <td><?= e($r['department_name'] ?? '—') ?></td>
                <td><?= e(substr((string) $r['time_in'], 11, 5)) ?></td>
                <td><?= e(substr((string) $r['time_out'], 11, 5)) ?></td>
                <td><?= e((string) $r['hours_worked']) ?></td>
                <td><?= (int) $r['late_minutes'] ?></td>
                <td><?= (int) $r['undertime_minutes'] ?></td>
                <td><?= (int) $r['overtime_minutes'] ?></td>
                <td><span class="badge bg-secondary"><?= e($r['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$records): ?><tr><td colspan="11" class="text-center py-4 text-muted">No records.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
    <?php View::partial('pagination', ['pagination' => $pagination, 'baseUrl' => '/attendance']); ?>
</div>