<?php
declare(strict_types=1);

/**
 * Environment + config helpers.
 * Every function is guarded with function_exists() so this file is safe to
 * require more than once (e.g. accidentally via different bootstrap paths).
 */

if (!function_exists('load_env')) {
    /**
     * Load a simple KEY=VALUE .env file into getenv()/$_ENV.
     * Silently does nothing if the file is missing.
     */
    function load_env(string $path): void
    {
        static $loaded = [];
        if (isset($loaded[$path])) {
            return; // idempotent — never parse the same file twice
        }
        $loaded[$path] = true;

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
            $key   = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2 && (
                    ($value[0] === '"' && $value[-1] === '"') ||
                    ($value[0] === "'" && $value[-1] === "'")
                )) {
                $value = substr($value, 1, -1);
            }
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return match (strtolower($value)) {
            'true',  '(true)'  => true,
            'false', '(false)' => false,
            'null',  '(null)'  => null,
            'empty', '(empty)' => '',
            default            => $value,
        };
    }
}

if (!function_exists('config')) {
    /**
     * Read a value from the loaded configuration array.
     * Example: config('app.url'), config('db.host', '127.0.0.1').
     * Loads config/config.php once and caches it.
     */
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $cfg = null;
        if ($cfg === null) {
            $cfg = require BASE_PATH . '/config/config.php';
        }
        if ($key === null) {
            return $cfg;
        }
        $segments = explode('.', $key);
        $value = $cfg;
        foreach ($segments as $seg) {
            if (!is_array($value) || !array_key_exists($seg, $value)) {
                return $default;
            }
            $value = $value[$seg];
        }
        return $value;
    }
}