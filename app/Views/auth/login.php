<div class="d-flex align-items-center justify-content-center min-vh-100 bg-primary-subtle">
    <div class="card shadow-lg border-0" style="width:100%;max-width:420px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-people-fill text-primary" style="font-size:2.5rem;"></i>
                <h4 class="mt-2 mb-0">Philippine HRMS</h4>
                <p class="text-muted small">Sign in to your account</p>
            </div>

            <?php foreach (Flash::pull() as $f): ?>
                <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
            <?php endforeach; ?>

            <form method="post" action="<?= url('/login') ?>" novalidate>
                <?= CSRF::field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Sign In</button>
            </form>

            <div class="mt-4 small text-muted">
                <strong>Demo accounts (password: <code>Password123!</code>)</strong>
                <ul class="mb-0 ps-3">
                    <li>admin@example.com</li>
                    <li>hr@example.com</li>
                    <li>payroll@example.com</li>
                    <li>manager@example.com</li>
                    <li>employee@example.com</li>
                </ul>
            </div>
        </div>
    </div>
</div>