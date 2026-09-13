<?php
declare(strict_types=1);
define('BASE_PATH', __DIR__);

// Minimal bootstrap — do not load the full app, just config + DB.
require BASE_PATH . '/app/Helpers/env.php';
load_env(BASE_PATH . '/.env');

$config = require BASE_PATH . '/config/config.php';
$db = $config['db'];

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4",
        $db['user'],
        $db['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Throwable $e) {
    exit("DB connection failed: " . $e->getMessage() . "\n");
}

// 1) Generate a real hash for the demo password.
$plain = 'Password123!';
$hash  = password_hash($plain, PASSWORD_DEFAULT);
echo "Generated hash: $hash\n";

// 2) Update every demo account.
$emails = [
    'admin@example.com',
    'hr@example.com',
    'payroll@example.com',
    'manager@example.com',
    'employee@example.com',
];

$upd = $pdo->prepare(
    'UPDATE users
        SET password_hash = :h,
            failed_attempts = 0,
            locked_until = NULL,
            is_active = 1
      WHERE email = :e'
);

foreach ($emails as $email) {
    $upd->execute([':h' => $hash, ':e' => $email]);
    echo "Updated {$email} (rows affected: {$upd->rowCount()})\n";
}

// 3) Verify the update worked end to end.
$sel = $pdo->prepare('SELECT password_hash FROM users WHERE email = :e');
$sel->execute([':e' => 'admin@example.com']);
$stored = $sel->fetchColumn();

echo "\n--- Verification ---\n";
if (!$stored) {
    echo "!! No admin@example.com row found. Did you import database/seed.sql?\n";
    exit;
}
echo "Stored hash prefix: " . substr($stored, 0, 30) . "...\n";
echo "password_verify('Password123!', stored) = "
    . (password_verify($plain, $stored) ? "TRUE ✅" : "FALSE ❌") . "\n";

if (password_verify($plain, $stored)) {
    echo "\nLogin should now work with:\n";
    echo "  Email:    admin@example.com\n";
    echo "  Password: Password123!\n";
}