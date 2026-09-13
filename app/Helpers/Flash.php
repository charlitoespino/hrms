<?php
declare(strict_types=1);

final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function success(string $m): void { self::set('success', $m); }
    public static function error(string $m): void   { self::set('danger', $m); }
    public static function warning(string $m): void { self::set('warning', $m); }
    public static function info(string $m): void    { self::set('info', $m); }

    public static function pull(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $messages;
    }
}