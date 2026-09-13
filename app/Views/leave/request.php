<h4 class="mb-3">File Leave Request</h4>

<div class="row g-3">
    <div class="col-md-7">
        <form method="post" action="<?= url('/leave/request') ?>" class="card shadow-sm border-0">
            <div class="card-body">
                <?= CSRF::field() ?>
                <div class="mb-3">
                    <label class="form-label">Leave Type *</label>
                    <select name="leave_type_id" class="form-select" required>
                        <option value="">Select…</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= (int) $t['id'] ?>"><?= e($t['name']) ?> (<?= e((string) $t['default_days']) ?> days)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col"><label class="form-label">Start Date *</label><input type="date" name="start_date" class="form-control" required></div>
                    <div class="col"><label class="form-label">End Date *</label><input type="date" name="end_date" class="form-control" required></div>
                    <div class="col"><label class="form-label">Days *</label><input type="number" step="0.5" min="0.5" name="days" class="form-control" required></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" rows="3" class="form-control"></textarea>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <a class="btn btn-light" href="<?= url('/leave') ?>">Cancel</a>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">My Leave Balances</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($balances as $b): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e($b['leave_type_name']) ?></span>
                        <strong><?= e((string) $b['balance']) ?> / <?= e((string) $b['entitled']) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>