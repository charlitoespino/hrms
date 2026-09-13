<?php
declare(strict_types=1);

final class DashboardController
{
    public function index(): void
    {
        AuthMiddleware::handle();

        // If employee, show personal dashboard
        if (Auth::hasRole('employee') && !Auth::can('employees.view')) {
            $this->employeeDashboard();
            return;
        }

        $employeeModel = new Employee();
        $attendanceModel = new Attendance();
        $leaveModel = new Leave();
        $payrollModel = new Payroll();

        $data = [
            'title'             => 'Dashboard',
            'totalEmployees'    => $employeeModel->count(['deleted_at' => null]),
            'activeEmployees'   => $employeeModel->activeCount(),
            'onLeaveToday'      => $this->countOnLeaveToday(),
            'attendanceToday'   => $attendanceModel->todaySummary(),
            'pendingLeave'      => $leaveModel->pendingCount(),
            'currentPeriod'     => $payrollModel->currentPeriod(),
            'deptDistribution'  => $employeeModel->distributionByDepartment(),
        ];

        View::render('dashboard/index', $data);
    }

    private function employeeDashboard(): void
    {
        $employeeId = Auth::employeeId();
        if (!$employeeId) {
            View::render('dashboard/employee', ['title' => 'My Dashboard', 'noEmployee' => true]);
            return;
        }
        $attendanceModel = new Attendance();
        $leaveModel = new Leave();
        $payrollModel = new Payroll();

        $today = date('Y-m-d');
        $data = [
            'title'         => 'My Dashboard',
            'today'         => $attendanceModel->findByEmployeeAndDate($employeeId, $today),
            'balances'      => $leaveModel->balancesFor($employeeId),
            'recentPayslips'=> $payrollModel->payslipsForEmployee($employeeId, 3),
        ];
        View::render('dashboard/employee', $data);
    }

    private function countOnLeaveToday(): int
    {
        $today = date('Y-m-d');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM leave_requests
             WHERE status = 'Approved' AND :d BETWEEN start_date AND end_date"
        );
        $stmt->execute([':d' => $today]);
        return (int) $stmt->fetchColumn();
    }

    /** AJAX endpoint for dashboard chart data */
    public function stats(): void
    {
        AuthMiddleware::handle();
        Response::json([
            'departments' => (new Employee())->distributionByDepartment(),
            'attendance'  => (new Attendance())->todaySummary(),
        ]);
    }
}