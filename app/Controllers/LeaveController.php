<?php
declare(strict_types=1);

final class LeaveController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('leave.view');

        $isEmployeeOnly = !Auth::can('leave.approve') && Auth::hasRole('employee');
        $filters = $isEmployeeOnly
            ? ['employee_id' => Auth::employeeId()]
            : ['status' => Input::str('status', 'get'), 'supervisor_id' => Input::str('scope', 'get') === 'team' ? Auth::employeeId() : 0];

        $page = max(1, Input::int('page', 'get', 1));
        $result = (new Leave())->paginate($page, 15, array_filter($filters));

        View::render('leave/index', [
            'title'    => 'Leave Requests',
            'requests' => $result['items'],
            'pagination'=> $result,
            'filters'  => $filters,
        ]);
    }

    public function request(): void
    {
        AuthMiddleware::handle();
        $employeeId = Auth::employeeId();
        if (!$employeeId) Response::abort(403, 'No linked employee record.');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyRequest();
            $this->store($employeeId);
            return;
        }

        View::render('leave/request', [
            'title'      => 'File Leave Request',
            'types'      => (new Leave())->types(),
            'balances'   => (new Leave())->balancesFor($employeeId),
        ]);
    }

    private function store(int $employeeId): void
    {
        $data = [
            'leave_type_id' => Input::int('leave_type_id'),
            'start_date'    => Input::str('start_date'),
            'end_date'      => Input::str('end_date'),
            'days'          => Input::float('days'),
            'reason'        => Input::str('reason'),
        ];
        $validator = (new Validator($data))
            ->required('leave_type_id', 'Leave Type')
            ->required('start_date', 'Start Date')
            ->required('end_date', 'End Date')
            ->date('start_date', 'Start Date')
            ->date('end_date', 'End Date')
            ->custom('end_date', strtotime($data['end_date']) >= strtotime($data['start_date']), 'End date must not be before start date.')
            ->custom('days', $data['days'] > 0, 'Days must be greater than zero.');

        if ($validator->fails()) {
            Flash::error($validator->firstError());
            Response::redirect('/leave/request');
        }

        Database::pdo()->beginTransaction();
        try {
            $id = (new Leave())->insert([
                'employee_id'   => $employeeId,
                'leave_type_id' => $data['leave_type_id'],
                'start_date'    => $data['start_date'],
                'end_date'      => $data['end_date'],
                'days'          => $data['days'],
                'reason'        => $data['reason'],
                'status'        => 'Pending',
            ]);
            Database::pdo()->commit();
            Audit::log('leave.requested', 'leave', (string) $id);
            Flash::success('Leave request submitted.');
            Response::redirect('/leave');
        } catch (Throwable $e) {
            Database::pdo()->rollBack();
            Logger::error('Leave request failed: ' . $e->getMessage());
            Flash::error('Failed to submit leave request.');
            Response::redirect('/leave/request');
        }
    }

    public function approval(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('leave.approve');
        $page = max(1, Input::int('page', 'get', 1));
        $result = (new Leave())->paginate($page, 15, ['status' => 'Pending', 'supervisor_id' => Auth::employeeId()]);
        View::render('leave/approval', [
            'title'      => 'Leave Approvals',
            'requests'   => $result['items'],
            'pagination' => $result,
        ]);
    }

    public function approve(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('leave.approve');
        CSRF::verifyRequest();
        $id = Input::int('id');
        $service = new LeaveService(new Leave());
        $result = Auth::hasRole('manager') && !Auth::can('leave.manage')
            ? $service->approveByManager($id, (int) Auth::id())
            : $service->approveByHR($id, (int) Auth::id());
        Response::json($result, $result['ok'] ? 200 : 400);
    }

    public function reject(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('leave.approve');
        CSRF::verifyRequest();
        $id = Input::int('id');
        $remarks = Input::str('remarks');
        $result = (new LeaveService(new Leave()))->reject($id, (int) Auth::id(), $remarks);
        Response::json($result, $result['ok'] ? 200 : 400);
    }
}