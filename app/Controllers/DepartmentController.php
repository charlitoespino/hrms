<?php
declare(strict_types=1);

final class DepartmentController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require(['departments.manage','employees.view']);
        View::render('departments/index', [
            'title'       => 'Departments',
            'departments' => (new Department())->withHeadcount(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('departments.manage');
        CSRF::verifyRequest();

        $name = Input::str('name');
        $code = Input::str('code');
        if ($name === '' || $code === '') {
            Flash::error('Name and code are required.');
            Response::redirect('/departments');
        }
        try {
            $id = (new Department())->insert([
                'name' => $name, 'code' => strtoupper($code),
                'head_employee_id' => Input::int('head_employee_id') ?: null,
                'is_active' => Input::bool('is_active') ? 1 : 0,
            ]);
            Audit::log('department.created', 'departments', (string) $id, null, $name);
            Flash::success('Department created.');
        } catch (Throwable $e) {
            Logger::error('Department create: ' . $e->getMessage());
            Flash::error('Failed to create department. Code may already exist.');
        }
        Response::redirect('/departments');
    }

    public function update(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('departments.manage');
        CSRF::verifyRequest();
        $id = Input::int('id');
        (new Department())->update($id, [
            'name' => Input::str('name'),
            'code' => strtoupper(Input::str('code')),
            'head_employee_id' => Input::int('head_employee_id') ?: null,
            'is_active' => Input::bool('is_active') ? 1 : 0,
        ]);
        Audit::log('department.updated', 'departments', (string) $id);
        Flash::success('Department updated.');
        Response::redirect('/departments');
    }

    public function destroy(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('departments.manage');
        CSRF::verifyRequest();
        $id = Input::int('id');
        (new Department())->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        Audit::log('department.deleted', 'departments', (string) $id);
        Flash::success('Department archived.');
        Response::redirect('/departments');
    }
}