<?php
$current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = parse_url((require BASE_PATH . '/config/config.php')['app']['url'], PHP_URL_PATH) ?: '';
if ($base && str_starts_with($current, $base)) $current = substr($current, strlen($base));
$current = '/' . ltrim($current, '/');

$item = function (string $href, string $icon, string $label, ?string $perm = null) use ($current) {
    if ($perm !== null && !Auth::can($perm)) return;
    $active = str_starts_with($current, $href) ? 'active bg-primary text-white' : 'text-dark';
    echo '<li class="nav-item"><a class="nav-link ' . $active . ' rounded mx-2 my-1" href="' . url($href) . '"><i class="bi ' . $icon . '"></i> ' . e($label) . '</a></li>';
};
?>
<ul class="nav flex-column py-3">
    <?php $item('/dashboard', 'bi-speedometer2', 'Dashboard'); ?>
    <?php $item('/employees', 'bi-people', 'Employees', 'employees.view'); ?>
    <?php $item('/departments', 'bi-diagram-3', 'Departments', 'departments.manage'); ?>
    <?php $item('/positions', 'bi-briefcase', 'Positions', 'positions.manage'); ?>
    <?php $item('/attendance', 'bi-clock-history', 'Attendance', 'attendance.view'); ?>
    <?php if (Auth::employeeId()): $item('/attendance/my-attendance', 'bi-fingerprint', 'My Attendance'); endif; ?>
    <?php $item('/leave', 'bi-calendar-check', 'Leave', 'leave.view'); ?>
    <?php if (Auth::employeeId()): $item('/leave/request', 'bi-calendar-plus', 'File Leave'); endif; ?>
    <?php if (Auth::can('leave.approve')): $item('/leave/approval', 'bi-check2-square', 'Leave Approvals'); endif; ?>
    <?php $item('/holidays', 'bi-calendar-event', 'Holidays', 'holidays.manage'); ?>
    <?php $item('/payroll', 'bi-cash-stack', 'Payroll', 'payroll.view'); ?>
    <?php if (Auth::employeeId()): $item('/payroll/my-payslips', 'bi-receipt', 'My Payslips'); endif; ?>
    <?php $item('/users', 'bi-person-badge', 'Users', 'users.manage'); ?>
    <?php $item('/audit-logs', 'bi-journal-text', 'Audit Logs', 'audit.view'); ?>
</ul>