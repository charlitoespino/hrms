<h4 class="mb-3">Leave Approvals (Pending)</h4>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr>
            <th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Status</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td><?= e($r['employee_name']) ?></td>
                <td><?= e($r['leave_type_name']) ?></td>
                <td><?= e($r['start_date']) ?></td>
                <td><?= e($r['end_date']) ?></td>
                <td><?= e((string) $r['days']) ?></td>
                <td class="text-truncate" style="max-width:250px"><?= e($r['reason'] ?? '') ?></td>
                <td><span class="badge bg-warning text-dark"><?= e($r['status']) ?></span></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-success btn-approve" data-id="<?= (int) $r['id'] ?>">Approve</button>
                    <button class="btn btn-sm btn-outline-danger btn-reject" data-id="<?= (int) $r['id'] ?>">Reject</button>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$requests): ?><tr><td colspan="8" class="text-center py-4 text-muted">No pending requests.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('pagination', ['pagination' => $pagination, 'baseUrl' => '/leave/approval']); ?>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog"><form method="post" action="<?= url('/leave/reject') ?>" class="modal-content" id="rejectForm">
        <?= CSRF::field() ?>
        <input type="hidden" name="id" id="rejectId">
        <div class="modal-header"><h5 class="modal-title">Reject Request</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control" required></textarea></div>
        <div class="modal-footer"><button class="btn btn-danger">Reject</button></div>
    </form></div>
</div>