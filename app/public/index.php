<?php
declare(strict_types=1);

/**
 * Front controller / entry point.
 */

define('BASE_PATH', dirname(__DIR__));

// Load config first (also populates env)
$config = require BASE_PATH . '/config/config.php';

// Autoload helpers & core (very small PSR-4-ish loader)
spl_autoload_register(function (string $class): void {
    $dirs = [
        BASE_PATH . '/app/Controllers',
        BASE_PATH . '/app/Models',
        BASE_PATH . '/app/Services',
        BASE_PATH . '/app/Middleware',
        BASE_PATH . '/app/Helpers',
        BASE_PATH . '/app/Validators',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

// Load helper functions (env, url, e, etc.)
require BASE_PATH . '/app/Helpers/env.php';
require BASE_PATH . '/app/Helpers/View.php'; // also defines e() and url()

// Timezone
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Manila');

// Error handling
if (($config['app']['debug'] ?? false) === false) {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// Secure session configuration
session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => (bool) $config['session']['secure'],
    'httponly' => true,
    'samesite' => $config['session']['samesite'],
]);
session_start();

// Global exception handler
set_exception_handler(function (Throwable $e) use ($config): void {
    Logger::error('Uncaught exception: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
    $msg = ($config['app']['debug'] ?? false)
        ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
        : 'Something went wrong while processing your request. Please try again or contact the administrator.';
    http_response_code(500);
    echo '<!doctype html><html><body style="font-family:sans-serif;padding:2rem;">'
        . '<h2>System Error</h2><p>' . Security::escape($msg) . '</p>'
        . '<a href="javascript:history.back()">Go back</a></body></html>';
});

// Determine route
$router = require BASE_PATH . '/routes/web.php';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Strip base path (so /hrms/employees works when hosted in a subdir)
$basePath = parse_url($config['app']['url'], PHP_URL_PATH) ?: '';
if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}
$uri = '/' . ltrim($uri, '/');
if ($uri !== '/' && str_ends_with($uri, '/')) {
    $uri = rtrim($uri, '/');
}

$key = strtoupper($method) . ' ' . $uri;

if (!isset($router[$key])) {
    Response::abort(404);
}

[$controller, $action] = $router[$key];
if (!class_exists($controller) || !method_exists($controller, $action)) {
    Logger::error("Handler not found: $controller::$action");
    Response::abort(500);
}

try {
    (new $controller())->$action();
} catch (Throwable $e) {
    Logger::error('Controller error: ' . $e->getMessage(), [
        'controller' => $controller, 'action' => $action,
    ]);
    throw $e;
}