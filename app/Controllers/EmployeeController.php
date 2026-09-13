<?php
declare(strict_types=1);

final class EmployeeController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('employees.view');

        $page = max(1, Input::int('page', 'get', 1));
        $filters = [
            'q'             => Input::str('q', 'get'),
            'department_id' => Input::int('department_id', 'get'),
            'status'        => Input::str('status', 'get'),
        ];

        $result = (new Employee())->paginate($page, 15, $filters);
        View::render('employees/index', [
            'title'       => 'Employees',
            'employees'   => $result['items'],
            'pagination'  => $result,
            'filters'     => $filters,
            'departments' => (new Department())->allActive(),
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('employees.create');
        $this->renderForm(null);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('employees.create');
        CSRF::verifyRequest();

        $data = $this->collect();
        $validator = $this->validate($data);
        if ($validator->fails()) {
            Flash::error($validator->firstError());
            Response::redirect('/employees/create');
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $model = new Employee();
            $data['employee_code'] = $data['employee_code'] ?: $model->nextCode();
            $id = $model->insert($data);

            // Government IDs
            $pdo->prepare(
                'INSERT INTO employee_government_ids (employee_id, sss_number, philhealth_number, pagibig_number, tin_number)
                 VALUES (:e, :s, :p, :pi, :t)'
            )->execute([
                ':e'  => $id,
                ':s'  => Input::str('sss_number'),
                ':p'  => Input::str('philhealth_number'),
                ':pi' => Input::str('pagibig_number'),
                ':t'  => Input::str('tin_number'),
            ]);

            $pdo->commit();
            Audit::log('employee.created', 'employees', (string) $id, null, $data['employee_code']);
            Flash::success('Employee created successfully.');
            Response::redirect('/employees/view?id=' . $id);
        } catch (Throwable $e) {
            $pdo->rollBack();
            Logger::error('Employee create failed: ' . $e->getMessage());
            Flash::error('Failed to create employee.');
            Response::redirect('/employees/create');
        }
    }

    public function edit(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('employees.edit');
        $id = Input::int('id', 'get');
        $employee = (new Employee())->findDetailed($id);
        if (!$employee) Response::abort(404);
        $this->renderForm($employee);
    }

    public function update(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('employees.edit');
        CSRF::verifyRequest();

        $id = Input::int('id');
        $data = $this->collect();
        $validator = $this->validate($data, $id);
        if ($validator->fails()) {
            Flash::error($validator->firstError());
            Response::redirect('/employees/edit?id=' . $id);
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $model = new Employee();
            $old = $model->find($id);
            $model->update($id, $data);

            $pdo->prepare(
                'INSERT INTO employee_government_ids (employee_id, sss_number, philhealth_number, pagibig_number, tin_number)
                 VALUES (:e, :s, :p, :pi, :t)
                 ON DUPLICATE KEY UPDATE sss_number = VALUES(sss_number), philhealth_number = VALUES(philhealth_number),
                                         pagibig_number = VALUES(pagibig_number), tin_number = VALUES(tin_number)'
            )->execute([
                ':e'  => $id,
                ':s'  => Input::str('sss_number'),
                ':p'  => Input::str('philhealth_number'),
                ':pi' => Input::str('pagibig_number'),
                ':t'  => Input::str('tin_number'),
            ]);

            $pdo->commit();
            Audit::log('employee.updated', 'employees', (string) $id, json_encode($old), json_encode($data));
            Flash::success('Employee updated.');
            Response::redirect('/employees/view?id=' . $id);
        } catch (Throwable $e) {
            $pdo->rollBack();
            Logger::error('Employee update failed: ' . $e->getMessage());
            Flash::error('Failed to update employee.');
            Response::redirect('/employees/edit?id=' . $id);
        }
    }

    public function view(): void
    {
        AuthMiddleware::handle();
        $id = Input::int('id', 'get');
        $employee = (new Employee())->findDetailed($id);

        if (!$employee) Response::abort(404);

        // Employees may only view their own record
        if (Auth::hasRole('employee') && !Auth::can('employees.view')) {
            if ((int) ($employee['id']) !== (int) Auth::employeeId()) {
                Response::abort(403);
            }
        } else {
            PermissionMiddleware::require('employees.view');
        }

        View::render('employees/view', [
            'title'      => 'Employee: ' . $employee['employee_code'],
            'employee'   => $employee,
            'attendance' => (new Attendance())->forEmployee($id, null, null),
            'balances'   => (new Leave())->balancesFor($id),
            'payslips'   => (new Payroll())->payslipsForEmployee($id, 10),
        ]);
    }

    public function destroy(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('employees.delete');
        CSRF::verifyRequest();

        $id = Input::int('id');
        // soft-delete: mark separated + deleted_at
        Database::pdo()->prepare(
            "UPDATE employees SET deleted_at = NOW(), employment_status = 'Separated', date_separated = CURDATE() WHERE id = :id"
        )->execute([':id' => $id]);

        Audit::log('employee.deleted', 'employees', (string) $id);
        Flash::success('Employee archived.');
        Response::redirect('/employees');
    }

    private function renderForm(?array $employee): void
    {
        View::render('employees/form', [
            'title'      => $employee ? 'Edit Employee' : 'New Employee',
            'employee'   => $employee,
            'departments'=> (new Department())->allActive(),
            'positions'  => (new Position())->allWithDepartment(),
            'branches'   => (new Branch())->allActive(),
            'types'      => Database::pdo()->query('SELECT * FROM employment_types WHERE is_active=1')->fetchAll(),
        ]);
    }

    private function collect(): array
    {
        return [
            'employee_code'       => Input::str('employee_code'),
            'first_name'          => Input::str('first_name'),
            'middle_name'         => Input::str('middle_name') ?: null,
            'last_name'           => Input::str('last_name'),
            'suffix'              => Input::str('suffix') ?: null,
            'birth_date'          => Input::str('birth_date') ?: null,
            'gender'              => Input::str('gender') ?: null,
            'civil_status'        => Input::str('civil_status') ?: null,
            'nationality'         => Input::str('nationality') ?: 'Filipino',
            'email'               => Input::str('email') ?: null,
            'mobile_number'       => Input::str('mobile_number') ?: null,
            'address_line'        => Input::str('address_line') ?: null,
            'city'                => Input::str('city') ?: null,
            'province'            => Input::str('province') ?: null,
            'postal_code'         => Input::str('postal_code') ?: null,
            'emergency_contact_name'     => Input::str('emergency_contact_name') ?: null,
            'emergency_contact_relation' => Input::str('emergency_contact_relation') ?: null,
            'emergency_contact_number'   => Input::str('emergency_contact_number') ?: null,
            'department_id'       => Input::int('department_id') ?: null,
            'position_id'         => Input::int('position_id') ?: null,
            'branch_id'           => Input::int('branch_id') ?: null,
            'employment_type_id'  => Input::int('employment_type_id') ?: null,
            'supervisor_id'       => Input::int('supervisor_id') ?: null,
            'date_hired'          => Input::str('date_hired') ?: null,
            'regularization_date' => Input::str('regularization_date') ?: null,
            'employment_status'   => Input::str('employment_status', 'post', 'Probationary'),
            'basic_salary'        => Input::float('basic_salary'),
            'pay_frequency'       => Input::str('pay_frequency', 'post', 'Semi-Monthly'),
        ];
    }

    private function validate(array $data, ?int $id = null): Validator
    {
        $v = (new Validator($data))
            ->required('first_name', 'First Name')
            ->required('last_name', 'Last Name')
            ->required('date_hired', 'Date Hired')
            ->date('date_hired', 'Date Hired')
            ->numeric('basic_salary', 'Basic Salary')
            ->min('basic_salary', 0, 'Basic Salary');
        if (!empty($data['email'])) {
            $v->email('email', 'Email');
        }
        return $v;
    }
}