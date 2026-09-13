<?php
declare(strict_types=1);

final class Attendance extends BaseModel
{
    protected string $table = 'attendance';

    public function findByEmployeeAndDate(int $employeeId, string $date): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM attendance WHERE employee_id = :e AND attendance_date = :d LIMIT 1'
        );
        $stmt->execute([':e' => $employeeId, ':d' => $date]);
        return $stmt->fetch() ?: null;
    }

    public function forEmployee(int $employeeId, ?string $from = null, ?string $to = null): array
    {
        $sql = 'SELECT * FROM attendance WHERE employee_id = :e';
        $params = [':e' => $employeeId];
        if ($from) { $sql .= ' AND attendance_date >= :from'; $params[':from'] = $from; }
        if ($to)   { $sql .= ' AND attendance_date <= :to';   $params[':to']   = $to; }
        $sql .= ' ORDER BY attendance_date DESC LIMIT 500';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function paginate(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['employee_id'])) {
            $where[] = 'a.employee_id = :e';
            $params[':e'] = (int) $filters['employee_id'];
        }
        if (!empty($filters['department_id'])) {
            $where[] = 'e.department_id = :d';
            $params[':d'] = (int) $filters['department_id'];
        }
        if (!empty($filters['from'])) {
            $where[] = 'a.attendance_date >= :from';
            $params[':from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where[] = 'a.attendance_date <= :to';
            $params[':to'] = $filters['to'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'a.status = :st';
            $params[':st'] = $filters['status'];
        }
        $whereSql = implode(' AND ', $where);

        $sql = "SELECT a.*, e.employee_code, CONCAT(e.first_name,' ',e.last_name) AS employee_name,
                       d.name AS department_name
                FROM attendance a
                INNER JOIN employees e ON e.id = a.employee_id
                LEFT JOIN departments d ON d.id = e.department_id
                WHERE $whereSql
                ORDER BY a.attendance_date DESC, e.employee_code ASC
                LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM attendance a INNER JOIN employees e ON e.id = a.employee_id WHERE $whereSql"
        );
        foreach ($params as $k => $v) $countStmt->bindValue($k, $v);
        $countStmt->execute();

        return ['items' => $items, 'total' => (int) $countStmt->fetchColumn(), 'page' => $page, 'per_page' => $perPage];
    }

    public function todaySummary(): array
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS total FROM attendance WHERE attendance_date = :d GROUP BY status"
        );
        $stmt->execute([':d' => $today]);
        $rows = $stmt->fetchAll();
        $summary = ['Present'=>0,'Late'=>0,'Absent'=>0,'On Leave'=>0,'Holiday'=>0,'Rest Day'=>0,'Official Business'=>0,'Work From Home'=>0,'Half Day'=>0];
        foreach ($rows as $r) $summary[$r['status']] = (int) $r['total'];
        return $summary;
    }
}