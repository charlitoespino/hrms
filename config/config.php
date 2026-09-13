<?php
declare(strict_types=1);

/**
 * Application configuration.
 *
 * This file MUST be pure data — no function declarations.
 * Functions (env, load_env, config) live in app/Helpers/env.php.
 *
 * BASE_PATH and the env() helper must already be defined before this file
 * is required. See public/index.php for the correct bootstrap order.
 */

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
        'host'    => env('DB_HOST', '127.0.0.1'),
        'port'    => (int) env('DB_PORT', '3306'),
        'name'    => env('DB_NAME', 'hrms'),
        'user'    => env('DB_USER', 'root'),
        'pass'    => env('DB_PASS', ''),
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