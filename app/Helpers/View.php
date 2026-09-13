<?php
declare(strict_types=1);

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'app'): void
    {
        $templatePath = BASE_PATH . '/app/Views/' . $template . '.php';
        if (!is_file($templatePath)) {
            Logger::error('View not found: ' . $template);
            Response::abort(500, 'View not found.');
        }

        extract($data, EXTR_SKIP);
        $config = require BASE_PATH . '/config/config.php';

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        $layoutPath = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
        if (is_file($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    public static function partial(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require BASE_PATH . '/app/Views/partials/' . $template . '.php';
    }
}

/** Shorthand escape helper used in views */
function e(?string $value): string
{
    return Security::escape($value);
}

/** Shorthand URL helper */
function url(string $path = ''): string
{
    static $config;
    $config ??= require BASE_PATH . '/config/config.php';
    $base = rtrim($config['app']['url'], '/');
    return $base . '/' . ltrim($path, '/');
}

/** Shorthand asset URL */
function asset(string $path): string
{
    return url('public/' . ltrim($path, '/'));
}