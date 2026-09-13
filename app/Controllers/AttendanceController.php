<?php
declare(strict_types=1);

final class AttendanceController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require(['attendance.view','attendance.manage']);

        $page = max(1, Input::int('page', 'get', 1));
        $filters = [
            'employee_id'   => Input::int('employee_id', 'get'),
            'department_id' => Input::int('department_id', 'get'),
            'from'          => Input::str('from', 'get'),
            'to'            => Input::str('to', 'get'),
            'status'        => Input::str('status', 'get'),
        ];
        $result = (new Attendance())->paginate($page, 20, $filters);
        View::render('attendance/index', [
            'title'       => 'Attendance',
            'records'     => $result['items'],
            'pagination'  => $result,
            'filters'     => $filters,
            'departments' => (new Department())->allActive(),
            'summary'     => (new Attendance())->todaySummary(),
        ]);
    }

    public function myAttendance(): void
    {
        AuthMiddleware::handle();
        $employeeId = Auth::employeeId();
        if (!$employeeId) {
            Response::abort(403, 'No linked employee record.');
        }
        $from = Input::str('from', 'get') ?: null;
        $to   = Input::str('to', 'get') ?: null;

        View::render('attendance/my-attendance', [
            'title'    => 'My Attendance',
            'records'  => (new Attendance())->forEmployee($employeeId, $from, $to),
            'today'    => (new Attendance())->findByEmployeeAndDate($employeeId, date('Y-m-d')),
            'from'     => $from,
            'to'       => $to,
        ]);
    }

    public function clockIn(): void
    {
        AuthMiddleware::handle();
        CSRF::verifyRequest();
        $employeeId = Auth::employeeId();
        if (!$employeeId) Response::json(['ok'=>false,'error'=>'No linked employee.'], 400);
        $result = (new AttendanceService(new Attendance(), new Holiday()))->clockIn($employeeId);
        Response::json($result, $result['ok'] ? 200 : 400);
    }

    public function clockOut(): void
    {
        AuthMiddleware::handle();
        CSRF::verifyRequest();
        $employeeId = Auth::employeeId();
        if (!$employeeId) Response::json(['ok'=>false,'error'=>'No linked employee.'], 400);
        $result = (new AttendanceService(new Attendance(), new Holiday()))->clockOut($employeeId);
        Response::json($result, $result['ok'] ? 200 : 400);
    }
}