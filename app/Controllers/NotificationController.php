<?php
declare(strict_types=1);

final class NotificationController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        Response::json([
            'unread' => Notification::unreadCount((int) Auth::id()),
            'items'  => Notification::latest((int) Auth::id(), 10),
        ]);
    }

    public function markRead(): void
    {
        AuthMiddleware::handle();
        CSRF::verifyRequest();
        $id = Input::int('id');
        Database::pdo()->prepare(
            'UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :u'
        )->execute([':id' => $id, ':u' => Auth::id()]);
        Response::json(['ok' => true]);
    }

    public function markAllRead(): void
    {
        AuthMiddleware::handle();
        CSRF::verifyRequest();
        Database::pdo()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :u')
            ->execute([':u' => Auth::id()]);
        Response::json(['ok' => true]);
    }
}