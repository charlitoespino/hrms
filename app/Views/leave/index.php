<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Leave Requests</h4>
    <?php if (Auth::employeeId()): ?>
        <a class="btn btn-primary" href="<?= url('/leave/request') ?>"><i class="bi bi-plus-lg"></i> File Leave</a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr>
            <th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Filed</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td><?= e($r['employee_name'] ?? '—') ?><br><small class="text-muted"><?= e($r['employee_code'] ?? '') ?></small></td>
                <td><?= e($r['leave_type_name']) ?></td>
                <td><?= e($r['start_date']) ?></td>
                <td><?= e($r['end_date']) ?></td>
                <td><?= e((string) $r['days']) ?></td>
                <td>
                    <?php
                    $b = match ($r['status']) {
                        'Approved' => 'success',
                        'Pending' => 'warning',
                        'Manager Approved' => 'info',
                        'Rejected' => 'danger',
                        default => 'secondary',
                    };
                    ?>
                    <span class="badge bg-<?= $b ?>"><?= e($r['status']) ?></span>
                </td>
                <td><?= e(substr((string) $r['created_at'], 0, 16)) ?></td>
                <td class="text-end">
                    <?php if (Auth::can('leave.approve') && in_array($r['status'], ['Pending','Manager Approved'], true)): ?>
                        <button class="btn btn-sm btn-success btn-approve" data-id="<?= (int) $r['id'] ?>">Approve</button>
                        <button class="btn btn-sm btn-outline-danger btn-reject" data-id="<?= (int) $r['id'] ?>">Reject</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php View::partial('pagination', ['pagination' => $pagination, 'baseUrl' => '/leave']); ?>
</div>