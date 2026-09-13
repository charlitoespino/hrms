<?php
declare(strict_types=1);

final class UserController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('users.manage');
        $users = Database::pdo()->query(
            'SELECT u.*, r.display_name AS role_name, e.employee_code
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             LEFT JOIN employees e ON e.id = u.employee_id
             WHERE u.deleted_at IS NULL ORDER BY u.id'
        )->fetchAll();
        View::render('users/index', [
            'title' => 'User Accounts',
            'users' => $users,
            'roles' => (new Role())->all(),
            'employees' => Database::pdo()->query(
                "SELECT id, employee_code, CONCAT(first_name,' ',last_name) AS name
                 FROM employees WHERE deleted_at IS NULL ORDER BY employee_code"
            )->fetchAll(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('users.manage');
        CSRF::verifyRequest();

        $data = [
            'email'     => Input::str('email'),
            'full_name' => Input::str('full_name'),
            'role_id'   => Input::int('role_id'),
            'employee_id' => Input::int('employee_id') ?: null,
            'is_active' => 1,
        ];
        $password = (string) Input::post('password', '');

        $v = (new Validator($data))
            ->required('email', 'Email')->email('email', 'Email')
            ->required('full_name', 'Full Name')
            ->required('role_id', 'Role')
            ->custom('password', strlen($password) >= 8, 'Password must be at least 8 characters.');

        if ($v->fails()) {
            Flash::error($v->firstError());
            Response::redirect('/users');
        }

        try {
            $data['password_hash'] = Security::hashPassword($password);
            $id = (new User())->insert($data);
            Audit::log('user.created', 'users', (string) $id, null, $data['email']);
            Flash::success('User account created.');
        } catch (Throwable $e) {
            Logger::error('User create failed: ' . $e->getMessage());
            Flash::error('Failed to create user. Email may already exist.');
        }
        Response::redirect('/users');
    }

    public function toggle(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('users.manage');
        CSRF::verifyRequest();
        $id = Input::int('id');
        $user = (new User())->find($id);
        if (!$user) Response::json(['ok' => false], 404);
        if ((int) $user['id'] === (int) Auth::id()) {
            Response::json(['ok' => false, 'error' => 'You cannot deactivate your own account.'], 400);
        }
        (new User())->update($id, ['is_active' => ((int) $user['is_active']) === 1 ? 0 : 1]);
        Audit::log('user.toggled', 'users', (string) $id);
        Response::json(['ok' => true]);
    }
}