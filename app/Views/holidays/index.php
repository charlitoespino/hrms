<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Holidays — <?= (int) $year ?></h4>
    <form class="d-flex gap-2">
        <input type="number" name="year" class="form-control" value="<?= (int) $year ?>" style="width:110px">
        <button class="btn btn-outline-primary">Go</button>
        <?php if (Auth::can('holidays.manage')): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newHoliday" type="button"><i class="bi bi-plus-lg"></i> Add</button>
        <?php endif; ?>
    </form>
</div>

<div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Date</th><th>Name</th><th>Type</th><th>Description</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($holidays as $h): ?>
            <tr>
                <td><?= e($h['holiday_date']) ?></td>
                <td><?= e($h['name']) ?></td>
                <td><span class="badge bg-info text-dark"><?= e($h['holiday_type']) ?></span></td>
                <td><?= e($h['description'] ?? '') ?></td>
                <td class="text-end">
                    <?php if (Auth::can('holidays.manage')): ?>
                    <form method="post" action="<?= url('/holidays/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this holiday?')">
                        <?= CSRF::field() ?>
                        <input type="hidden" name="id" value="<?= (int) $h['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$holidays): ?><tr><td colspan="5" class="text-center py-4 text-muted">No holidays for <?= (int) $year ?>.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div></div>

<?php if (Auth::can('holidays.manage')): ?>
<div class="modal fade" id="newHoliday" tabindex="-1">
    <div class="modal-dialog"><form method="post" action="<?= url('/holidays/store') ?>" class="modal-content">
        <?= CSRF::field() ?>
        <div class="modal-header"><h5 class="modal-title">New Holiday</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-2"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Date *</label><input type="date" name="holiday_date" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Type *</label>
                <select name="holiday_type" class="form-select" required>
                    <option>Regular Holiday</option>
                    <option>Special Non-Working Holiday</option>
                    <option>Special Working Holiday</option>
                    <option>Company Holiday</option>
                </select>
            </div>
            <div class="mb-2"><label class="form-label">Description</label><input name="description" class="form-control"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary" type="submit">Save</button></div>
    </form></div>
</div>
<?php endif; ?>