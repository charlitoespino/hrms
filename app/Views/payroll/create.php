<h4 class="mb-3">Create Payroll Period</h4>

<form method="post" action="<?= url('/payroll/create') ?>" class="card shadow-sm border-0" style="max-width:720px;">
    <div class="card-body">
        <?= CSRF::field() ?>
        <div class="mb-3"><label class="form-label">Period Name *</label>
            <input name="period_name" class="form-control" placeholder="e.g. September 1-15, 2026" required></div>
        <div class="row g-2 mb-3">
            <div class="col"><label class="form-label">Date From *</label><input type="date" name="date_from" class="form-control" required></div>
            <div class="col"><label class="form-label">Date To *</label><input type="date" name="date_to" class="form-control" required></div>
            <div class="col"><label class="form-label">Pay Date *</label><input type="date" name="pay_date" class="form-control" required></div>
        </div>
        <div class="mb-3"><label class="form-label">Frequency</label>
            <select name="pay_frequency" class="form-select">
                <option>Semi-Monthly</option><option>Monthly</option><option>Weekly</option><option>Daily</option>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control"></textarea></div>
    </div>
    <div class="card-footer bg-white text-end">
        <a class="btn btn-light" href="<?= url('/payroll') ?>">Cancel</a>
        <button class="btn btn-primary">Create</button>
    </div>
</form>