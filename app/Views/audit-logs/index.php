<h4 class="mb-3">Audit Logs</h4>

<form class="card shadow-sm border-0 mb-3"><div class="card-body row g-2">
    <div class="col-md-3"><input name="module" class="form-control" placeholder="Module" value="<?= e($filters['module']) ?>"></div>
    <div class="col-md-3"><input name="action" class="form-control" placeholder="Action contains…" value="<?= e($filters['action']) ?>"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
</div></form>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light"><tr>
            <th>When</th><th>User</th><th>Action</th><th>Module</th><th>Record</th><th>IP</th>
        </tr></thead>
        <tbody>
        <?php foreach ($logs as $l): ?>
            <tr>
                <td><?= e($l['created_at']) ?></td>
                <td><?= e($l['user_email'] ?? '—') ?></td>
                <td><code><?= e($l['action']) ?></code></td>
                <td><?= e($l['module']) ?></td>
                <td><?= e($l['record_id'] ?? '') ?></td>
                <td><?= e($l['ip_address'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php View::partial('pagination', ['pagination' => $pagination, 'baseUrl' => '/audit-logs']); ?>
</div>