<?php
declare(strict_types=1);

final class PermissionMiddleware
{
    /**
     * @param string|array<string> $permission
     */
    public static function require($permission): void
    {
        $permissions = is_array($permission) ? $permission : [$permission];
        foreach ($permissions as $perm) {
            if (Auth::can($perm)) {
                return;
            }
        }
        Logger::warning('Permission denied', [
            'user' => Auth::id(),
            'required' => $permissions,
        ]);
        Response::abort(403);
    }

    public static function requireRole(string ...$roles): void
    {
        if (!Auth::hasRole(...$roles)) {
            Response::abort(403);
        }
    }
}