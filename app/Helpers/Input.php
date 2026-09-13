<?php
declare(strict_types=1);

final class Input
{
    public static function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public static function str(string $key, string $source = 'post', string $default = ''): string
    {
        $bag = $source === 'get' ? $_GET : $_POST;
        $v = $bag[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    public static function int(string $key, string $source = 'post', int $default = 0): int
    {
        $bag = $source === 'get' ? $_GET : $_POST;
        return isset($bag[$key]) && is_numeric($bag[$key]) ? (int) $bag[$key] : $default;
    }

    public static function float(string $key, string $source = 'post', float $default = 0.0): float
    {
        $bag = $source === 'get' ? $_GET : $_POST;
        return isset($bag[$key]) && is_numeric($bag[$key]) ? (float) $bag[$key] : $default;
    }

    public static function bool(string $key, string $source = 'post'): bool
    {
        $bag = $source === 'get' ? $_GET : $_POST;
        return !empty($bag[$key]);
    }

    public static function all(string $source = 'post'): array
    {
        return $source === 'get' ? $_GET : $_POST;
    }
}