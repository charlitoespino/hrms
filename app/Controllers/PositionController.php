<?php
declare(strict_types=1);

final class PositionController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require(['positions.manage','employees.view']);
        View::render('positions/index', [
            'title'       => 'Positions',
            'positions'   => (new Position())->allWithDepartment(),
            'departments' => (new Department())->allActive(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('positions.manage');
        CSRF::verifyRequest();
        try {
            (new Position())->insert([
                'name' => Input::str('name'),
                'code' => strtoupper(Input::str('code')),
                'department_id' => Input::int('department_id') ?: null,
                'salary_min' => Input::float('salary_min') ?: null,
                'salary_max' => Input::float('salary_max') ?: null,
                'is_active' => 1,
            ]);
            Audit::log('position.created', 'positions');
            Flash::success('Position created.');
        } catch (Throwable $e) {
            Flash::error('Failed to create position.');
        }
        Response::redirect('/positions');
    }

    public function update(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('positions.manage');
        CSRF::verifyRequest();
        $id = Input::int('id');
        (new Position())->update($id, [
            'name' => Input::str('name'),
            'code' => strtoupper(Input::str('code')),
            'department_id' => Input::int('department_id') ?: null,
            'salary_min' => Input::float('salary_min') ?: null,
            'salary_max' => Input::float('salary_max') ?: null,
            'is_active' => Input::bool('is_active') ? 1 : 0,
        ]);
        Audit::log('position.updated', 'positions', (string) $id);
        Flash::success('Position updated.');
        Response::redirect('/positions');
    }

    public function destroy(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('positions.manage');
        CSRF::verifyRequest();
        $id = Input::int('id');
        (new Position())->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        Audit::log('position.deleted', 'positions', (string) $id);
        Flash::success('Position archived.');
        Response::redirect('/positions');
    }
}