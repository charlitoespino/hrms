<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Payroll Periods</h4>
    <?php if (Auth::can('payroll.process')): ?>
        <a class="btn btn-primary" href="<?= url('/payroll/create') ?>"><i class="bi bi-plus-lg"></i> New Period</a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr>
            <th>Period</th><th>From</th><th>To</th><th>Pay Date</th><th>Frequency</th><th>Status</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($periods as $p): ?>
            <tr>
                <td><?= e($p['period_name']) ?></td>
                <td><?= e($p['date_from']) ?></td>
                <td><?= e($p['date_to']) ?></td>
                <td><?= e($p['pay_date']) ?></td>
                <td><?= e($p['pay_frequency']) ?></td>
                <td>
                    <?php
                    $b = match ($p['status']) {
                        'Locked' => 'dark',
                        'Approved' => 'success',
                        'For Review' => 'warning',
                        'Processing' => 'info',
                        'Cancelled' => 'danger',
                        default => 'secondary',
                    };
                    ?>
                    <span class="badge bg-<?= $b ?>"><?= e($p['status']) ?></span>
                </td>
                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('/payroll/view?id=' . $p['id']) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php View::partial('pagination', ['pagination' => $pagination, 'baseUrl' => '/payroll']); ?>
</div>