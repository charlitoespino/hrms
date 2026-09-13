<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><?= e($period['period_name']) ?></h4>
        <small class="text-muted"><?= e($period['date_from']) ?> → <?= e($period['date_to']) ?> · Pay <?= e($period['pay_date']) ?></small>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-secondary align-self-center"><?= e($period['status']) ?></span>
        <?php if (Auth::can('payroll.process') && in_array($period['status'], ['Draft','Processing','For Review'], true)): ?>
            <button class="btn btn-outline-primary" id="btnProcess">Process Payroll</button>
        <?php endif; ?>
        <?php if (Auth::can('payroll.approve') && $period['status'] === 'For Review'): ?>
            <button class="btn btn-success" id="btnApprove">Approve</button>
        <?php endif; ?>
        <?php if (Auth::can('payroll.approve') && $period['status'] === 'Approved'): ?>
            <button class="btn btn-dark" id="btnLock">Lock & Generate Payslips</button>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Total Gross</div><div class="fs-5 fw-semibold">₱<?= number_format($totalGross, 2) ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Total Deductions</div><div class="fs-5 fw-semibold">₱<?= number_format($totalDed, 2) ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Total Net</div><div class="fs-5 fw-semibold">₱<?= number_format($totalNet, 2) ?></div></div></div></div>
</div>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light"><tr>
            <th>Code</th><th>Employee</th><th>Dept</th><th>Basic</th><th>Gross</th>
            <th>SSS</th><th>PhilHealth</th><th>Pag-IBIG</th><th>Tax</th><th>Deductions</th><th>Net</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($items as $p): ?>
            <tr>
                <td><?= e($p['employee_code']) ?></td>
                <td><?= e($p['employee_name']) ?></td>
                <td><?= e($p['department_name'] ?? '—') ?></td>
                <td>₱<?= number_format((float) $p['basic_salary'], 2) ?></td>
                <td>₱<?= number_format((float) $p['gross_pay'], 2) ?></td>
                <td>₱<?= number_format((float) $p['sss_employee'], 2) ?></td>
                <td>₱<?= number_format((float) $p['philhealth_employee'], 2) ?></td>
                <td>₱<?= number_format((float) $p['pagibig_employee'], 2) ?></td>
                <td>₱<?= number_format((float) $p['withholding_tax'], 2) ?></td>
                <td>₱<?= number_format((float) $p['total_deductions'], 2) ?></td>
                <td><strong>₱<?= number_format((float) $p['net_pay'], 2) ?></strong></td>
                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('/payroll/payslip?id=' . $p['id']) ?>">Payslip</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?><tr><td colspan="12" class="text-center py-4 text-muted">Not yet processed.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div></div>