<?php
declare(strict_types=1);

final class Notification
{
    public static function send(int $userId, string $title, string $message, string $link = '', string $type = 'info'): void
    {
        try {
            $pdo = Database::pdo();
            $stmt = $pdo->prepare(
                'INSERT INTO notifications (user_id, title, message, link, type)
                 VALUES (:u, :t, :m, :l, :ty)'
            );
            $stmt->execute([
                ':u' => $userId, ':t' => $title, ':m' => $message,
                ':l' => $link, ':ty' => $type,
            ]);
        } catch (Throwable $e) {
            Logger::error('Notification failed: ' . $e->getMessage());
        }
    }

    public static function unreadCount(int $userId): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :u AND is_read = 0');
        $stmt->execute([':u' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function latest(int $userId, int $limit = 10): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'SELECT * FROM notifications WHERE user_id = :u ORDER BY created_at DESC LIMIT :lim'
        );
        $stmt->bindValue(':u', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}