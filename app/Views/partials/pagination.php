<?php
/** @var array $pagination */
$page = (int) $pagination['page'];
$perPage = (int) $pagination['per_page'];
$total = (int) $pagination['total'];
$lastPage = max(1, (int) ceil($total / $perPage));
$buildQuery = function (int $p) {
    $q = $_GET;
    $q['page'] = $p;
    return '?' . http_build_query($q);
};
if ($lastPage <= 1) return;
?>
<div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <div class="text-muted small">Showing page <?= $page ?> of <?= $lastPage ?> (<?= $total ?> records)</div>
    <nav>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= e($buildQuery(max(1, $page - 1))) ?>">Previous</a>
            </li>
            <?php for ($i = max(1, $page - 2); $i <= min($lastPage, $page + 2); $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= e($buildQuery($i)) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $lastPage ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= e($buildQuery(min($lastPage, $page + 1))) ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>