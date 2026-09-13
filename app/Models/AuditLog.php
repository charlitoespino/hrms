<?php
declare(strict_types=1);

final class AuditLog extends BaseModel
{
    protected string $table = 'audit_logs';

    public function paginate(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['module'])) {
            $where[] = 'module = :m';
            $params[':m'] = $filters['module'];
        }
        if (!empty($filters['action'])) {
            $where[] = 'action LIKE :a';
            $params[':a'] = '%' . $filters['action'] . '%';
        }
        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare(
            "SELECT * FROM audit_logs WHERE $whereSql ORDER BY id DESC LIMIT :lim OFFSET :off"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM audit_logs WHERE $whereSql");
        foreach ($params as $k => $v) $countStmt->bindValue($k, $v);
        $countStmt->execute();

        return ['items' => $items, 'total' => (int) $countStmt->fetchColumn(), 'page' => $page, 'per_page' => $perPage];
    }
}