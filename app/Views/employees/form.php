<h4 class="mb-3"><?= $employee ? 'Edit Employee' : 'New Employee' ?></h4>

<form method="post" action="<?= url($employee ? '/employees/edit' : '/employees/create') ?>" class="card shadow-sm border-0">
    <div class="card-body">
        <?= CSRF::field() ?>
        <?php if ($employee): ?><input type="hidden" name="id" value="<?= (int) $employee['id'] ?>"><?php endif; ?>

        <h6 class="text-primary mt-2">Personal Information</h6>
        <div class="row g-2">
            <div class="col-md-3"><label class="form-label">Employee Code (auto if blank)</label>
                <input class="form-control" name="employee_code" value="<?= e($employee['employee_code'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">First Name *</label>
                <input class="form-control" name="first_name" required value="<?= e($employee['first_name'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Middle Name</label>
                <input class="form-control" name="middle_name" value="<?= e($employee['middle_name'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Last Name *</label>
                <input class="form-control" name="last_name" required value="<?= e($employee['last_name'] ?? '') ?>"></div>
            <div class="col-md-2"><label class="form-label">Suffix</label>
                <input class="form-control" name="suffix" value="<?= e($employee['suffix'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Birth Date</label>
                <input type="date" class="form-control" name="birth_date" value="<?= e($employee['birth_date'] ?? '') ?>"></div>
            <div class="col-md-2"><label class="form-label">Gender</label>
                <select class="form-select" name="gender">
                    <option value="">—</option>
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option <?= ($employee['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-2"><label class="form-label">Civil Status</label>
                <select class="form-select" name="civil_status">
                    <option value="">—</option>
                    <?php foreach (['Single','Married','Widowed','Separated','Divorced'] as $c): ?>
                        <option <?= ($employee['civil_status'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-3"><label class="form-label">Nationality</label>
                <input class="form-control" name="nationality" value="<?= e($employee['nationality'] ?? 'Filipino') ?>"></div>
        </div>

        <h6 class="text-primary mt-4">Contact & Address</h6>
        <div class="row g-2">
            <div class="col-md-3"><label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= e($employee['email'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Mobile</label>
                <input class="form-control" name="mobile_number" value="<?= e($employee['mobile_number'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Address</label>
                <input class="form-control" name="address_line" value="<?= e($employee['address_line'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">City</label>
                <input class="form-control" name="city" value="<?= e($employee['city'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Province</label>
                <input class="form-control" name="province" value="<?= e($employee['province'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Postal Code</label>
                <input class="form-control" name="postal_code" value="<?= e($employee['postal_code'] ?? '') ?>"></div>
        </div>

        <h6 class="text-primary mt-4">Emergency Contact</h6>
        <div class="row g-2">
            <div class="col-md-4"><label class="form-label">Name</label>
                <input class="form-control" name="emergency_contact_name" value="<?= e($employee['emergency_contact_name'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">Relation</label>
                <input class="form-control" name="emergency_contact_relation" value="<?= e($employee['emergency_contact_relation'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">Number</label>
                <input class="form-control" name="emergency_contact_number" value="<?= e($employee['emergency_contact_number'] ?? '') ?>"></div>
        </div>

        <h6 class="text-primary mt-4">Employment</h6>
        <div class="row g-2">
            <div class="col-md-3"><label class="form-label">Department</label>
                <select class="form-select" name="department_id">
                    <option value="">—</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int) $d['id'] ?>" <?= (int) ($employee['department_id'] ?? 0) === (int) $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-3"><label class="form-label">Position</label>
                <select class="form-select" name="position_id">
                    <option value="">—</option>
                    <?php foreach ($positions as $p): ?>
                        <option value="<?= (int) $p['id'] ?>" <?= (int) ($employee['position_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-3"><label class="form-label">Branch</label>
                <select class="form-select" name="branch_id">
                    <option value="">—</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= (int) $b['id'] ?>" <?= (int) ($employee['branch_id'] ?? 0) === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-3"><label class="form-label">Employment Type</label>
                <select class="form-select" name="employment_type_id">
                    <option value="">—</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= (int) $t['id'] ?>" <?= (int) ($employee['employment_type_id'] ?? 0) === (int) $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-3"><label class="form-label">Date Hired *</label>
                <input type="date" class="form-control" name="date_hired" required value="<?= e($employee['date_hired'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Regularization Date</label>
                <input type="date" class="form-control" name="regularization_date" value="<?= e($employee['regularization_date'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Employment Status</label>
                <select class="form-select" name="employment_status">
                    <?php foreach (['Probationary','Regular','Contractual','Project-Based','Part-Time','Casual','Separated'] as $s): ?>
                        <option <?= ($employee['employment_status'] ?? 'Probationary') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-3"><label class="form-label">Pay Frequency</label>
                <select class="form-select" name="pay_frequency">
                    <?php foreach (['Monthly','Semi-Monthly','Weekly','Daily'] as $s): ?>
                        <option <?= ($employee['pay_frequency'] ?? 'Semi-Monthly') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-3"><label class="form-label">Basic Salary (₱) *</label>
                <input type="number" step="0.01" min="0" class="form-control" name="basic_salary" required value="<?= e((string) ($employee['basic_salary'] ?? '0')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Supervisor ID (optional)</label>
                <input type="number" class="form-control" name="supervisor_id" value="<?= e((string) ($employee['supervisor_id'] ?? '')) ?>"></div>
        </div>

        <h6 class="text-primary mt-4">Government IDs (sample)</h6>
        <div class="row g-2">
            <div class="col-md-3"><label class="form-label">SSS Number</label>
                <input class="form-control" name="sss_number" value="<?= e($employee['sss_number'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">PhilHealth Number</label>
                <input class="form-control" name="philhealth_number" value="<?= e($employee['philhealth_number'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">Pag-IBIG Number</label>
                <input class="form-control" name="pagibig_number" value="<?= e($employee['pagibig_number'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="form-label">TIN</label>
                <input class="form-control" name="tin_number" value="<?= e($employee['tin_number'] ?? '') ?>"></div>
        </div>
    </div>
    <div class="card-footer bg-white text-end">
        <a class="btn btn-light" href="<?= url('/employees') ?>">Cancel</a>
        <button class="btn btn-primary" type="submit">Save</button>
    </div>
</form>