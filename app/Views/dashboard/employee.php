<h4 class="mb-3">Welcome back!</h4>

<?php if (!empty($noEmployee)): ?>
    <div class="alert alert-warning">Your account is not linked to an employee record. Contact HR.</div>
<?php else: ?>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Today's Attendance</div>
            <div class="card-body">
                <p class="mb-1">Time In: <strong><?= e($today['time_in'] ?? '—') ?></strong></p>
                <p class="mb-3">Time Out: <strong><?= e($today['time_out'] ?? '—') ?></strong></p>
                <button class="btn btn-success btn-sm me-1" id="btnClockIn">Clock In</button>
                <button class="btn btn-danger btn-sm" id="btnClockOut">Clock Out</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Leave Balances</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($balances as $b): ?>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span><?= e($b['leave_type_name']) ?></span>
                        <strong><?= e((string) $b['balance']) ?> days</strong>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Latest Payslips</div>
            <div class="card-body">
                <?php if (!$recentPayslips): ?><p class="text-muted mb-0">No payslips yet.</p><?php endif; ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentPayslips as $p): ?>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span><?= e($p['period_name']) ?></span>
                        <a href="<?= url('/payroll/payslip?id=' . $p['id']) ?>">₱<?= number_format((float) $p['net_pay'], 2) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>