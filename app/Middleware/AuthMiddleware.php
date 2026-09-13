<?php
declare(strict_types=1);

final class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            if (self::isAjax()) {
                Response::json(['error' => 'Unauthenticated'], 401);
            }
            Flash::warning('Please sign in to continue.');
            Response::redirect('/login');
        }

        // Idle timeout
        $config = require BASE_PATH . '/config/config.php';
        $lifetime = (int) ($config['session']['lifetime'] ?? 7200);
        $loginAt = $_SESSION['auth_user']['login_at'] ?? 0;
        if ($lifetime > 0 && (time() - $loginAt) > $lifetime) {
            Auth::logout();
            Flash::warning('Your session expired. Please sign in again.');
            Response::redirect('/login');
        }
        $_SESSION['auth_user']['login_at'] = time();
    }

    private static function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}