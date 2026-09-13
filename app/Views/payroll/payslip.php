<div class="container my-4" style="max-width:820px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                <div>
                    <h4 class="mb-0"><?= e($company['name']) ?></h4>
                    <div class="text-muted small"><?= e($company['address']) ?></div>
                    <div class="text-muted small">TIN: <?= e($company['tin']) ?></div>
                </div>
                <div class="text-end">
                    <h5 class="mb-0">PAYSLIP</h5>
                    <div class="text-muted small"><?= e($payroll['period_name']) ?></div>
                    <div class="text-muted small">Pay Date: <?= e($payroll['pay_date']) ?></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="small text-muted">Employee</div>
                    <div class="fw-semibold"><?= e($payroll['employee_name']) ?></div>
                    <div class="small">Code: <?= e($payroll['employee_code']) ?></div>
                    <div class="small">Dept: <?= e($payroll['department_name'] ?? '—') ?></div>
                    <div class="small">Position: <?= e($payroll['position_name'] ?? '—') ?></div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="small">SSS: <?= e($payroll['sss_number'] ?? '—') ?></div>
                    <div class="small">PhilHealth: <?= e($payroll['philhealth_number'] ?? '—') ?></div>
                    <div class="small">Pag-IBIG: <?= e($payroll['pagibig_number'] ?? '—') ?></div>
                    <div class="small">TIN: <?= e($payroll['tin_number'] ?? '—') ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-1">Earnings</h6>
                    <table class="table table-sm">
                        <?php foreach ($items as $it): if ($it['item_type'] !== 'Earning') continue; ?>
                        <tr><td><?= e($it['item_name']) ?></td><td class="text-end">₱<?= number_format((float) $it['amount'], 2) ?></td></tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold border-top"><td>Gross Pay</td><td class="text-end">₱<?= number_format((float) $payroll['gross_pay'], 2) ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="border-bottom pb-1">Deductions</h6>
                    <table class="table table-sm">
                        <?php foreach ($items as $it): if ($it['item_type'] !== 'Deduction') continue; ?>
                        <tr><td><?= e($it['item_name']) ?></td><td class="text-end">₱<?= number_format((float) $it['amount'], 2) ?></td></tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold border-top"><td>Total Deductions</td><td class="text-end">₱<?= number_format((float) $payroll['total_deductions'], 2) ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="text-end border-top pt-3 mt-2">
                <div class="text-muted small">NET PAY</div>
                <div class="fs-3 fw-bold">₱<?= number_format((float) $payroll['net_pay'], 2) ?></div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary" onclick="window.print()">Print</button>
            </div>
        </div>
    </div>
</div>