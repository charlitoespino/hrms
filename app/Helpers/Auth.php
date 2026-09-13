<?php
declare(strict_types=1);

final class Auth
{
    public const SESSION_USER_KEY = 'auth_user';

    public static function check(): bool
    {
        return !empty($_SESSION[self::SESSION_USER_KEY]['id']);
    }

    public static function id(): ?int
    {
        return $_SESSION[self::SESSION_USER_KEY]['id'] ?? null;
    }

    public static function user(): ?array
    {
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION[self::SESSION_USER_KEY]['role'] ?? null;
    }

    public static function employeeId(): ?int
    {
        $eid = $_SESSION[self::SESSION_USER_KEY]['employee_id'] ?? null;
        return $eid !== null ? (int) $eid : null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER_KEY] = [
            'id'           => (int) $user['id'],
            'employee_id'  => $user['employee_id'] !== null ? (int) $user['employee_id'] : null,
            'email'        => $user['email'],
            'full_name'    => $user['full_name'],
            'role'         => $user['role_name'],
            'role_id'      => (int) $user['role_id'],
            'permissions'  => $user['permissions'] ?? [],
            'login_at'     => time(),
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
        }
        session_destroy();
    }

    public static function can(string $permission): bool
    {
        $perms = $_SESSION[self::SESSION_USER_KEY]['permissions'] ?? [];
        return in_array($permission, $perms, true);
    }

    public static function hasRole(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }
}