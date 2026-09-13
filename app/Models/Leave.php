<?php
declare(strict_types=1);

final class Leave extends BaseModel
{
    protected string $table = 'leave_requests';

    public function types(): array
    {
        return $this->db->query('SELECT * FROM leave_types WHERE is_active = 1 ORDER BY name')->fetchAll();
    }

    public function balancesFor(int $employeeId, ?int $year = null): array
    {
        $year ??= (int) date('Y');
        $stmt = $this->db->prepare(
            'SELECT lb.*, lt.name AS leave_type_name, lt.code AS leave_type_code
             FROM leave_balances lb
             INNER JOIN leave_types lt ON lt.id = lb.leave_type_id
             WHERE lb.employee_id = :e AND lb.year = :y
             ORDER BY lt.name'
        );
        $stmt->execute([':e' => $employeeId, ':y' => $year]);
        return $stmt->fetchAll();
    }

    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['employee_id'])) {
            $where[] = 'lr.employee_id = :e';
            $params[':e'] = (int) $filters['employee_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'lr.status = :st';
            $params[':st'] = $filters['status'];
        }
        if (!empty($filters['supervisor_id'])) {
            $where[] = 'e.supervisor_id = :sup';
            $params[':sup'] = (int) $filters['supervisor_id'];
        }
        $whereSql = implode(' AND ', $where);

        $sql = "SELECT lr.*, lt.name AS leave_type_name,
                       e.employee_code, CONCAT(e.first_name,' ',e.last_name) AS employee_name,
                       d.name AS department_name
                FROM leave_requests lr
                INNER JOIN leave_types lt ON lt.id = lr.leave_type_id
                INNER JOIN employees e ON e.id = lr.employee_id
                LEFT JOIN departments d ON d.id = e.department_id
                WHERE $whereSql
                ORDER BY lr.created_at DESC
                LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM leave_requests lr INNER JOIN employees e ON e.id = lr.employee_id WHERE $whereSql"
        );
        foreach ($params as $k => $v) $countStmt->bindValue($k, $v);
        $countStmt->execute();

        return ['items' => $items, 'total' => (int) $countStmt->fetchColumn(), 'page' => $page, 'per_page' => $perPage];
    }

    public function findDetailed(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT lr.*, lt.name AS leave_type_name, lt.code AS leave_type_code,
                    e.employee_code, CONCAT(e.first_name,' ',e.last_name) AS employee_name,
                    d.name AS department_name, p.name AS position_name
             FROM leave_requests lr
             INNER JOIN leave_types lt ON lt.id = lr.leave_type_id
             INNER JOIN employees e ON e.id = lr.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN positions p ON p.id = e.position_id
             WHERE lr.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function pendingCount(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM leave_requests WHERE status IN ('Pending','Manager Approved')"
        )->fetchColumn();
    }

    /** Update balance used when approved. Must run inside transaction. */
    public function consumeBalance(int $employeeId, int $leaveTypeId, int $year, float $days): void
    {
        $stmt = $this->db->prepare(
            'UPDATE leave_balances SET used = used + :d, balance = balance - :d
             WHERE employee_id = :e AND leave_type_id = :t AND year = :y'
        );
        $stmt->execute([':d' => $days, ':e' => $employeeId, ':t' => $leaveTypeId, ':y' => $year]);
    }
}