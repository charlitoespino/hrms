<h4 class="mb-3">My Attendance</h4>

<div class="card shadow-sm border-0 mb-3"><div class="card-body">
    <h6>Today — <?= e(date('Y-m-d')) ?></h6>
    <p class="mb-1">Time In: <strong><?= e(substr((string) ($today['time_in'] ?? ''), 11, 8) ?: '—') ?></strong></p>
    <p class="mb-3">Time Out: <strong><?= e(substr((string) ($today['time_out'] ?? ''), 11, 8) ?: '—') ?></strong></p>
    <button class="btn btn-success btn-sm" id="btnClockIn">Clock In</button>
    <button class="btn btn-danger btn-sm" id="btnClockOut">Clock Out</button>
</div></div>

<form class="card shadow-sm border-0 mb-3"><div class="card-body row g-2">
    <div class="col-md-3"><input type="date" name="from" class="form-control" value="<?= e($from) ?>"></div>
    <div class="col-md-3"><input type="date" name="to" class="form-control" value="<?= e($to) ?>"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
</div></form>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>Date</th><th>In</th><th>Out</th><th>Hours</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($records as $r): ?>
            <tr>
                <td><?= e($r['attendance_date']) ?></td>
                <td><?= e(substr((string) $r['time_in'], 11, 5)) ?></td>
                <td><?= e(substr((string) $r['time_out'], 11, 5)) ?></td>
                <td><?= e((string) $r['hours_worked']) ?></td>
                <td><?= e($r['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>