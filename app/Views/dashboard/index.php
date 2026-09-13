<h3 class="mb-4">HR Dashboard</h3>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Total Employees', $totalEmployees, 'bi-people', 'primary'],
        ['Active Employees', $activeEmployees, 'bi-person-check', 'success'],
        ['On Leave Today', $onLeaveToday, 'bi-calendar-x', 'info'],
        ['Present Today', $attendanceToday['Present'] ?? 0, 'bi-check-circle', 'success'],
        ['Late Today', $attendanceToday['Late'] ?? 0, 'bi-clock', 'warning'],
        ['Absent Today', $attendanceToday['Absent'] ?? 0, 'bi-x-circle', 'danger'],
        ['Pending Leave', $pendingLeave, 'bi-hourglass-split', 'secondary'],
    ];
    foreach ($cards as [$label, $val, $icon, $color]):
    ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small"><?= e($label) ?></div>
                        <div class="fs-4 fw-semibold"><?= (int) $val ?></div>
                    </div>
                    <i class="bi <?= e($icon) ?> fs-2 text-<?= e($color) ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Employees by Department</div>
            <div class="card-body"><canvas id="deptChart" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">Attendance Today</div>
            <div class="card-body"><canvas id="attChart" height="120"></canvas></div>
        </div>
    </div>
</div>

<?php if ($currentPeriod): ?>
<div class="card shadow-sm border-0 mt-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <div class="text-muted small">Current Payroll Period</div>
            <div class="fw-semibold"><?= e($currentPeriod['period_name']) ?> — <?= e($currentPeriod['status']) ?></div>
        </div>
        <a class="btn btn-outline-primary" href="<?= url('/payroll/view?id=' . $currentPeriod['id']) ?>">View</a>
    </div>
</div>
<?php endif; ?>

<script>
const deptData = <?= json_encode($deptDistribution) ?>;
const attData  = <?= json_encode($attendanceToday) ?>;
</script>