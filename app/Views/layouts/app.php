<?php /** @var string $content */ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'HRMS') ?> — <?= e($config['app']['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('public/assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand ms-2" href="<?= url('/dashboard') ?>">
            <i class="bi bi-people-fill"></i> <?= e($config['app']['name']) ?>
        </a>
        <div class="d-flex align-items-center gap-3 text-white">
            <button class="btn btn-link text-white position-relative" id="notifBtn" type="button">
                <i class="bi bi-bell fs-5"></i>
                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" id="notifCount" style="display:none">0</span>
            </button>
            <div class="dropdown">
                <a class="text-white dropdown-toggle" data-bs-toggle="dropdown" href="#">
                    <?= e(Auth::user()['full_name'] ?? 'Guest') ?>
                    <small class="text-white-50 d-block" style="font-size:.75rem"><?= e(Auth::role() ?? '') ?></small>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('/payroll/my-payslips') ?>"><i class="bi bi-receipt"></i> My Payslips</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="post" action="<?= url('/logout') ?>" class="px-3 py-1">
                            <?= CSRF::field() ?>
                            <button class="btn btn-sm btn-outline-danger w-100" type="submit"><i class="bi bi-box-arrow-right"></i> Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 d-none d-lg-block bg-white border-end min-vh-100 p-0">
            <?php View::partial('sidebar'); ?>
        </aside>

        <!-- Mobile sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarNav">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">
                <?php View::partial('sidebar'); ?>
            </div>
        </div>

        <main class="col-lg-10 py-4 px-3 px-lg-4">
            <?php foreach (Flash::pull() as $f): ?>
                <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show">
                    <?= e($f['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.HRMS = {
        baseUrl: <?= json_encode(rtrim($config['app']['url'], '/')) ?>,
        csrf: <?= json_encode(CSRF::token()) ?>
    };
</script>
<script src="<?= url('public/assets/js/app.js') ?>"></script>
</body>
</html>