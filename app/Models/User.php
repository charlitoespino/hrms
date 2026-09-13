<?php
declare(strict_types=1);

final class User extends BaseModel
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.email = :e AND u.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([':e' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function permissionsForRole(int $roleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.name FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = :r'
        );
        $stmt->execute([':r' => $roleId]);
        return array_column($stmt->fetchAll(), 'name');
    }

    public function registerFailedAttempt(int $userId, int $maxAttempts, int $lockoutMinutes): void
    {
        $stmt = $this->db->prepare('SELECT failed_attempts FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
        $attempts = (int) $stmt->fetchColumn() + 1;

        $lockedUntil = null;
        if ($attempts >= $maxAttempts) {
            $lockedUntil = date('Y-m-d H:i:s', time() + ($lockoutMinutes * 60));
            $attempts = 0; // reset counter after lock
        }
        $upd = $this->db->prepare('UPDATE users SET failed_attempts = :a, locked_until = :l WHERE id = :id');
        $upd->execute([':a' => $attempts, ':l' => $lockedUntil, ':id' => $userId]);
    }

    public function resetFailedAttempts(int $userId, string $ip): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET failed_attempts = 0, locked_until = NULL, last_login_at = NOW(), last_login_ip = :ip WHERE id = :id'
        );
        $stmt->execute([':ip' => $ip, ':id' => $userId]);
    }

    public function isLocked(array $user): bool
    {
        if (empty($user['locked_until'])) return false;
        return strtotime($user['locked_until']) > time();
    }
}