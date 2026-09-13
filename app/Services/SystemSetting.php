<?php
declare(strict_types=1);

final class SystemSetting
{
    private array $cache = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }
        $stmt = Database::pdo()->prepare('SELECT setting_value FROM system_settings WHERE setting_key = :k LIMIT 1');
        $stmt->execute([':k' => $key]);
        $row = $stmt->fetch();
        $value = $row ? $row['setting_value'] : $default;
        $this->cache[$key] = $value;
        return $value;
    }

    public function set(string $key, string $value): void
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO system_settings (setting_key, setting_value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        $stmt->execute([':k' => $key, ':v' => $value]);
        $this->cache[$key] = $value;
    }

    public function allGrouped(): array
    {
        $rows = Database::pdo()->query('SELECT * FROM system_settings ORDER BY setting_group, setting_key')->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['setting_group']][] = $row;
        }
        return $grouped;
    }
}