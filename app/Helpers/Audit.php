<?php
declare(strict_types=1);

final class Audit
{
    public static function log(
        string $action,
        string $module,
        ?string $recordId = null,
        ?string $old = null,
        ?string $new = null
    ): void {
        try {
            $pdo = Database::pdo();
            $stmt = $pdo->prepare(
                'INSERT INTO audit_logs
                 (user_id, user_email, action, module, record_id, old_value, new_value, ip_address, user_agent)
                 VALUES (:uid, :email, :action, :module, :rid, :oldv, :newv, :ip, :ua)'
            );
            $stmt->execute([
                ':uid'    => Auth::id(),
                ':email'  => Auth::user()['email'] ?? null,
                ':action' => $action,
                ':module' => $module,
                ':rid'    => $recordId,
                ':oldv'   => $old,
                ':newv'   => $new,
                ':ip'     => Security::clientIp(),
                ':ua'     => Security::userAgent(),
            ]);
        } catch (Throwable $e) {
            Logger::error('Audit log failed: ' . $e->getMessage());
        }
    }
}