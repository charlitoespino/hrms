<?php
declare(strict_types=1);

/**
 * Front controller / entry point.
 *
 * Bootstrap order matters:
 *   1. BASE_PATH
 *   2. app/Helpers/env.php  (defines env(), load_env(), config())
 *   3. load_env()           (parses .env into getenv/$_ENV)
 *   4. config()             (loads config/config.php once and caches it)
 *   5. config/database.php, app/Helpers/View.php
 *   6. Class autoloader
 */

// ---------------------------------------------------------------
// 1) BASE_PATH must be defined before ANY require that uses it.
// ---------------------------------------------------------------
define('BASE_PATH', dirname(__DIR__));

// ---------------------------------------------------------------
// 2) Functions first. env.php declares env/load_env/config.
//    It is guarded with function_exists() so double-include is safe.
// ---------------------------------------------------------------
require BASE_PATH . '/app/Helpers/env.php';

// ---------------------------------------------------------------
// 3) Parse the .env file now that load_env() exists.
// ---------------------------------------------------------------
load_env(BASE_PATH . '/.env');

// ---------------------------------------------------------------
// 4) Load config once (pure array; no functions declared in it).
// ---------------------------------------------------------------
$config = config();

// ---------------------------------------------------------------
// 5) Files that don't contain a class (or whose class lives outside
//    the autoload paths) must be required explicitly.
// ---------------------------------------------------------------
require BASE_PATH . '/config/database.php';
require BASE_PATH . '/app/Helpers/View.php'; // also defines e(), url(), asset()

// ---------------------------------------------------------------
// 6) Autoloader for classes under app/.
// ---------------------------------------------------------------
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

// ---------------------------------------------------------------
// Timezone + error handling
// ---------------------------------------------------------------
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Manila');

if (($config['app']['debug'] ?? false) === false) {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// ---------------------------------------------------------------
// Secure session configuration
// ---------------------------------------------------------------
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

// ---------------------------------------------------------------
// Global exception handler — logs technical detail, shows friendly text.
// ---------------------------------------------------------------
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

// ---------------------------------------------------------------
// Router
// ---------------------------------------------------------------
$router = require BASE_PATH . '/routes/web.php';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Strip the app's base path so it also works when hosted in a subdirectory.
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