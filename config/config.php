<?php
declare(strict_types=1);

/**
 * Application configuration loader.
 * Reads .env values (very lightweight parser — no external dependency).
 */

function load_env(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Strip quotes
        if (strlen($value) >= 2 && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))) {
            $value = substr($value, 1, -1);
        }
        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

load_env(BASE_PATH . '/.env');

return [
    'app' => [
        'name'     => env('APP_NAME', 'Philippine HRMS'),
        'env'      => env('APP_ENV', 'production'),
        'debug'    => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
        'url'      => rtrim((string) env('APP_URL', 'http://localhost/hrms'), '/'),
        'timezone' => env('APP_TIMEZONE', 'Asia/Manila'),
        'key'      => env('APP_KEY', 'insecure-default-key-change-me'),
    ],
    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => (int) env('DB_PORT', '3306'),
        'name' => env('DB_NAME', 'hrms'),
        'user' => env('DB_USER', 'root'),
        'pass' => env('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'name'     => env('SESSION_NAME', 'HRMS_SESS'),
        'lifetime' => (int) env('SESSION_LIFETIME', '7200'),
        'secure'   => filter_var(env('SESSION_SECURE', 'false'), FILTER_VALIDATE_BOOLEAN),
        'samesite' => env('SESSION_SAMESITE', 'Lax'),
    ],
    'upload' => [
        'max_size' => (int) env('UPLOAD_MAX_SIZE', '5242880'),
        'path'     => BASE_PATH . '/public/uploads',
    ],
];