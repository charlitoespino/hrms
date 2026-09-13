<?php
declare(strict_types=1);

final class AuditLogController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('audit.view');
        $page = max(1, Input::int('page', 'get', 1));
        $filters = [
            'module' => Input::str('module', 'get'),
            'action' => Input::str('action', 'get'),
        ];
        $result = (new AuditLog())->paginate($page, 25, array_filter($filters));
        View::render('audit-logs/index', [
            'title'      => 'Audit Logs',
            'logs'       => $result['items'],
            'pagination' => $result,
            'filters'    => $filters,
        ]);
    }
}