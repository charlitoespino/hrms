<?php
declare(strict_types=1);

/**
 * Simple router. Maps "METHOD /path" to [Controller, method].
 * Query strings are not part of the path.
 */

$router = [];

function route(string $method, string $path, array $handler): void
{
    global $router;
    $router[strtoupper($method) . ' ' . $path] = $handler;
}

// Auth
route('GET',  '/login',            ['AuthController', 'showLogin']);
route('POST', '/login',            ['AuthController', 'login']);
route('POST', '/logout',           ['AuthController', 'logout']);

// Dashboard
route('GET',  '/dashboard',        ['DashboardController', 'index']);
route('GET',  '/api/dashboard/stats', ['DashboardController', 'stats']);
route('GET',  '/',                 ['DashboardController', 'index']);

// Employees
route('GET',  '/employees',                  ['EmployeeController', 'index']);
route('GET',  '/employees/create',           ['EmployeeController', 'create']);
route('POST', '/employees/create',           ['EmployeeController', 'store']);
route('GET',  '/employees/edit',             ['EmployeeController', 'edit']);
route('POST', '/employees/edit',             ['EmployeeController', 'update']);
route('GET',  '/employees/view',             ['EmployeeController', 'view']);
route('POST', '/employees/delete',           ['EmployeeController', 'destroy']);

// Departments
route('GET',  '/departments',                ['DepartmentController', 'index']);
route('POST', '/departments/store',          ['DepartmentController', 'store']);
route('POST', '/departments/update',         ['DepartmentController', 'update']);
route('POST', '/departments/delete',         ['DepartmentController', 'destroy']);

// Positions
route('GET',  '/positions',                  ['PositionController', 'index']);
route('POST', '/positions/store',            ['PositionController', 'store']);
route('POST', '/positions/update',           ['PositionController', 'update']);
route('POST', '/positions/delete',           ['PositionController', 'destroy']);

// Attendance
route('GET',  '/attendance',                 ['AttendanceController', 'index']);
route('GET',  '/attendance/my-attendance',   ['AttendanceController', 'myAttendance']);
route('POST', '/attendance/clock-in',        ['AttendanceController', 'clockIn']);
route('POST', '/attendance/clock-out',       ['AttendanceController', 'clockOut']);

// Leave
route('GET',  '/leave',                      ['LeaveController', 'index']);
route('GET',  '/leave/request',              ['LeaveController', 'request']);
route('POST', '/leave/request',              ['LeaveController', 'request']);
route('GET',  '/leave/approval',             ['LeaveController', 'approval']);
route('POST', '/leave/approve',              ['LeaveController', 'approve']);
route('POST', '/leave/reject',               ['LeaveController', 'reject']);

// Holidays
route('GET',  '/holidays',                   ['HolidayController', 'index']);
route('POST', '/holidays/store',             ['HolidayController', 'store']);
route('POST', '/holidays/update',            ['HolidayController', 'update']);
route('POST', '/holidays/delete',            ['HolidayController', 'destroy']);

// Payroll
route('GET',  '/payroll',                    ['PayrollController', 'index']);
route('GET',  '/payroll/create',             ['PayrollController', 'create']);
route('POST', '/payroll/create',             ['PayrollController', 'create']);
route('GET',  '/payroll/view',               ['PayrollController', 'view']);
route('POST', '/payroll/process',            ['PayrollController', 'process']);
route('POST', '/payroll/approve',            ['PayrollController', 'approve']);
route('POST', '/payroll/lock',               ['PayrollController', 'lock']);
route('GET',  '/payroll/payslip',            ['PayrollController', 'payslip']);
route('GET',  '/payroll/my-payslips',        ['PayrollController', 'myPayslips']);

// Users
route('GET',  '/users',                      ['UserController', 'index']);
route('POST', '/users/store',                ['UserController', 'store']);
route('POST', '/users/toggle',               ['UserController', 'toggle']);

// Audit
route('GET',  '/audit-logs',                 ['AuditLogController', 'index']);

// Notifications
route('GET',  '/api/notifications',          ['NotificationController', 'index']);
route('POST', '/api/notifications/read',     ['NotificationController', 'markRead']);
route('POST', '/api/notifications/read-all', ['NotificationController', 'markAllRead']);

return $router;