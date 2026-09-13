<?php
declare(strict_types=1);

function env(string $key, mixed $default = null): mixed
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    // Convert booleans / nulls
    $lower = strtolower($value);
    return match ($lower) {
        'true', '(true)'  => true,
        'false', '(false)' => false,
        'null', '(null)'  => null,
        'empty', '(empty)' => '',
        default => $value,
    };
}