<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><?= e($employee['first_name'] . ' ' . $employee['last_name']) ?>
        <small class="text-muted">(<?= e($employee['employee_code']) ?>)</small></h4>
    <?php if (Auth::can('employees.edit')): ?>
    <a class="btn btn-primary" href="<?= url('/employees/edit?id=' . $employee['id']) ?>"><i class="bi bi-pencil"></i> Edit</a>
    <?php endif; ?>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-profile">Profile</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-employment">Employment</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-gov">Government IDs</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-attendance">Attendance</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leave">Leave</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-payroll">Payroll</button></li>
</ul>

<div class="tab-content bg-white p-3 rounded shadow-sm border">
    <div class="tab-pane fade show active" id="tab-profile">
        <div class="row g-3">
            <div class="col-md-4"><strong>Email:</strong> <?= e($employee['email'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Mobile:</strong> <?= e($employee['mobile_number'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Birth Date:</strong> <?= e($employee['birth_date'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Gender:</strong> <?= e($employee['gender'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Civil Status:</strong> <?= e($employee['civil_status'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Nationality:</strong> <?= e($employee['nationality'] ?? '—') ?></div>
            <div class="col-12"><strong>Address:</strong> <?= e(trim(($employee['address_line'] ?? '') . ' ' . ($employee['city'] ?? '') . ' ' . ($employee['province'] ?? ''))) ?></div>
            <div class="col-12"><strong>Emergency Contact:</strong>
                <?= e($employee['emergency_contact_name'] ?? '—') ?>
                (<?= e($employee['emergency_contact_relation'] ?? '') ?>)
                <?= e($employee['emergency_contact_number'] ?? '') ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-employment">
        <div class="row g-3">
            <div class="col-md-4"><strong>Department:</strong> <?= e($employee['department_name'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Position:</strong> <?= e($employee['position_name'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Branch:</strong> <?= e($employee['branch_name'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Employment Type:</strong> <?= e($employee['employment_type_name'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Status:</strong> <?= e($employee['employment_status']) ?></div>
            <div class="col-md-4"><strong>Supervisor:</strong> <?= e(($employee['supervisor_first'] ?? '') . ' ' . ($employee['supervisor_last'] ?? '')) ?: '—' ?></div>
            <div class="col-md-4"><strong>Date Hired:</strong> <?= e($employee['date_hired'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Regularization:</strong> <?= e($employee['regularization_date'] ?? '—') ?></div>
            <div class="col-md-4"><strong>Basic Salary:</strong> ₱<?= number_format((float) $employee['basic_salary'], 2) ?></div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-gov">
        <div class="row g-3">
            <div class="col-md-3"><strong>SSS:</strong> <?= e($employee['sss_number'] ?? '—') ?></div>
            <div class="col-md-3"><strong>PhilHealth:</strong> <?= e($employee['philhealth_number'] ?? '—') ?></div>
            <div class="col-md-3"><strong>Pag-IBIG:</strong> <?= e($employee['pagibig_number'] ?? '—') ?></div>
            <div class="col-md-3"><strong>TIN:</strong> <?= e($employee['tin_number'] ?? '—') ?></div>
            <p class="text-muted small mt-3 mb-0">Values shown are SAMPLE/TEST DATA only.</p>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-attendance">
        <div class="table-responsive"><table class="table table-sm">
            <thead><tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Hours</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach (array_slice($attendance, 0, 30) as $a): ?>
                <tr>
                    <td><?= e($a['attendance_date']) ?></td>
                    <td><?= e($a['time_in'] ?? '—') ?></td>
                    <td><?= e($a['time_out'] ?? '—') ?></td>
                    <td><?= e((string) $a['hours_worked']) ?></td>
                    <td><span class="badge bg-secondary"><?= e($a['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table></div>
    </div>
    <div class="tab-pane fade" id="tab-leave">
        <ul class="list-group">
            <?php foreach ($balances as $b): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= e($b['leave_type_name']) ?></span>
                    <span>Entitled <?= e((string) $b['entitled']) ?> / Used <?= e((string) $b['used']) ?> /
                        <strong>Balance <?= e((string) $b['balance']) ?></strong></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="tab-pane fade" id="tab-payroll">
        <div class="table-responsive"><table class="table table-sm">
            <thead><tr><th>Period</th><th>Gross</th><th>Deductions</th><th>Net</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($payslips as $p): ?>
                <tr>
                    <td><?= e($p['period_name']) ?></td>
                    <td>₱<?= number_format((float) $p['gross_pay'], 2) ?></td>
                    <td>₱<?= number_format((float) $p['total_deductions'], 2) ?></td>
                    <td>₱<?= number_format((float) $p['net_pay'], 2) ?></td>
                    <td><a href="<?= url('/payroll/payslip?id=' . $p['id']) ?>">Payslip</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table></div>
    </div>
</div>