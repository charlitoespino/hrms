<?php
declare(strict_types=1);

final class CSRF
{
    public const TOKEN_KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = Security::randomToken(32);
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . Security::escape(self::token()) . '">';
    }

    public static function validate(?string $token): bool
    {
        if (empty($_SESSION[self::TOKEN_KEY]) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    public static function verifyRequest(): void
    {
        $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        if (!self::validate(is_string($token) ? $token : null)) {
            Response::abort(419, 'Security token mismatch. Please refresh and try again.');
        }
    }
}