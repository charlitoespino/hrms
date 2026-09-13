<?php
declare(strict_types=1);

final class Response
{
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $path, int $status = 302): never
    {
        if (!preg_match('#^https?://#i', $path)) {
            $base = rtrim((string) (require BASE_PATH . '/config/config.php')['app']['url'], '/');
            if (!str_starts_with($path, '/')) {
                $path = '/' . $path;
            }
            $path = $base . $path;
        }
        http_response_code($status);
        header('Location: ' . $path);
        exit;
    }

    public static function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        $config = require BASE_PATH . '/config/config.php';
        if (($config['app']['debug'] ?? false) === false) {
            $message = self::friendlyMessage($code, $message);
        }
        // Render a minimal error page
        $safe = Security::escape($message);
        echo <<<HTML
<!doctype html>
<html><head><meta charset="utf-8"><title>Error {$code}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body text-center p-5">
      <h1 class="display-4 text-danger">{$code}</h1>
      <p class="lead">{$safe}</p>
      <a class="btn btn-primary" href="javascript:history.back()">Go Back</a>
    </div>
  </div>
</div></body></html>
HTML;
        exit;
    }

    public static function friendlyMessage(int $code, string $fallback): string
    {
        return match ($code) {
            401 => 'You need to sign in to access this page.',
            403 => 'You are not authorized to access this page.',
            404 => 'The page you are looking for could not be found.',
            419 => 'Your session has expired. Please refresh and try again.',
            500 => 'Something went wrong while processing your request. Please try again or contact the administrator.',
            default => $fallback !== '' ? $fallback : 'An error occurred.',
        };
    }
}