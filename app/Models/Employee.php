<?php
declare(strict_types=1);

final class Employee extends BaseModel
{
    protected string $table = 'employees';

    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $where = ['e.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(e.employee_code LIKE :q OR e.first_name LIKE :q OR e.last_name LIKE :q OR e.email LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['department_id'])) {
            $where[] = 'e.department_id = :dept';
            $params[':dept'] = (int) $filters['department_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'e.employment_status = :status';
            $params[':status'] = $filters['status'];
        }

        $whereSql = implode(' AND ', $where);
        $sql = "SELECT e.*, d.name AS department_name, p.name AS position_name,
                       b.name AS branch_name, et.name AS employment_type_name
                FROM employees e
                LEFT JOIN departments d ON d.id = e.department_id
                LEFT JOIN positions p ON p.id = e.position_id
                LEFT JOIN branches b ON b.id = e.branch_id
                LEFT JOIN employment_types et ON et.id = e.employment_type_id
                WHERE $whereSql
                ORDER BY e.employee_code ASC
                LIMIT :lim OFFSET :off";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM employees e WHERE $whereSql");
        foreach ($params as $k => $v) $countStmt->bindValue($k, $v);
        $countStmt->execute();

        return [
            'items' => $rows,
            'total' => (int) $countStmt->fetchColumn(),
            'page'  => $page,
            'per_page' => $perPage,
        ];
    }

    public function findDetailed(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT e.*, d.name AS department_name, p.name AS position_name,
                    b.name AS branch_name, et.name AS employment_type_name,
                    s.first_name AS supervisor_first, s.last_name AS supervisor_last,
                    g.sss_number, g.philhealth_number, g.pagibig_number, g.tin_number
             FROM employees e
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN positions p ON p.id = e.position_id
             LEFT JOIN branches b ON b.id = e.branch_id
             LEFT JOIN employment_types et ON et.id = e.employment_type_id
             LEFT JOIN employees s ON s.id = e.supervisor_id
             LEFT JOIN employee_government_ids g ON g.employee_id = e.id
             WHERE e.id = :id AND e.deleted_at IS NULL
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function nextCode(): string
    {
        $stmt = $this->db->query('SELECT MAX(id) FROM employees');
        $next = ((int) $stmt->fetchColumn()) + 1;
        return 'EMP-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function activeCount(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL AND employment_status <> 'Separated'"
        )->fetchColumn();
    }

    public function distributionByDepartment(): array
    {
        return $this->db->query(
            "SELECT COALESCE(d.name,'Unassigned') AS label, COUNT(e.id) AS total
             FROM employees e
             LEFT JOIN departments d ON d.id = e.department_id
             WHERE e.deleted_at IS NULL AND e.employment_status <> 'Separated'
             GROUP BY d.name ORDER BY total DESC"
        )->fetchAll();
    }
}