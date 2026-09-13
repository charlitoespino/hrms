<h4 class="mb-3">My Payslips</h4>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Period</th><th>Pay Date</th><th>Gross</th><th>Deductions</th><th>Net</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($payslips as $p): ?>
            <tr>
                <td><?= e($p['period_name']) ?></td>
                <td><?= e($p['pay_date']) ?></td>
                <td>₱<?= number_format((float) $p['gross_pay'], 2) ?></td>
                <td>₱<?= number_format((float) $p['total_deductions'], 2) ?></td>
                <td><strong>₱<?= number_format((float) $p['net_pay'], 2) ?></strong></td>
                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('/payroll/payslip?id=' . $p['id']) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$payslips): ?><tr><td colspan="6" class="text-center py-4 text-muted">No payslips yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div></div>