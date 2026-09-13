<?php
declare(strict_types=1);

final class Payroll extends BaseModel
{
    protected string $table = 'payrolls';

    public function periodPaginate(int $page = 1, int $perPage = 15): array
    {
        $stmt = $this->db->prepare(
            'SELECT pp.*, u.full_name AS created_by_name
             FROM payroll_periods pp
             LEFT JOIN users u ON u.id = pp.created_by
             ORDER BY pp.date_from DESC LIMIT :lim OFFSET :off'
        );
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
        $total = (int) $this->db->query('SELECT COUNT(*) FROM payroll_periods')->fetchColumn();
        return ['items' => $items, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function period(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payroll_periods WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function itemsForPeriod(int $periodId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, e.employee_code, CONCAT(e.first_name,' ',e.last_name) AS employee_name,
                    d.name AS department_name, pos.name AS position_name
             FROM payrolls p
             INNER JOIN employees e ON e.id = p.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN positions pos ON pos.id = e.position_id
             WHERE p.payroll_period_id = :pp
             ORDER BY e.employee_code ASC"
        );
        $stmt->execute([':pp' => $periodId]);
        return $stmt->fetchAll();
    }

    public function findForEmployeePeriod(int $employeeId, int $payrollId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, pp.period_name, pp.date_from, pp.date_to, pp.pay_date,
                    e.employee_code, CONCAT(e.first_name,' ',e.last_name) AS employee_name,
                    d.name AS department_name, pos.name AS position_name,
                    g.sss_number, g.philhealth_number, g.pagibig_number, g.tin_number
             FROM payrolls p
             INNER JOIN payroll_periods pp ON pp.id = p.payroll_period_id
             INNER JOIN employees e ON e.id = p.employee_id
             LEFT JOIN departments d ON d.id = e.department_id
             LEFT JOIN positions pos ON pos.id = e.position_id
             LEFT JOIN employee_government_ids g ON g.employee_id = e.id
             WHERE p.id = :pid AND p.employee_id = :eid"
        );
        $stmt->execute([':pid' => $payrollId, ':eid' => $employeeId]);
        return $stmt->fetch() ?: null;
    }

    public function items(int $payrollId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM payroll_items WHERE payroll_id = :p ORDER BY item_type, id');
        $stmt->execute([':p' => $payrollId]);
        return $stmt->fetchAll();
    }

    public function payslipsForEmployee(int $employeeId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, pp.period_name, pp.date_from, pp.date_to, pp.pay_date
             FROM payrolls p
             INNER JOIN payroll_periods pp ON pp.id = p.payroll_period_id
             WHERE p.employee_id = :e AND pp.status IN ('Approved','Locked')
             ORDER BY pp.date_from DESC LIMIT :lim"
        );
        $stmt->bindValue(':e', $employeeId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function currentPeriod(): ?array
    {
        $stmt = $this->db->query(
            "SELECT * FROM payroll_periods WHERE status IN ('Processing','For Review','Approved')
             ORDER BY date_from DESC LIMIT 1"
        );
        return $stmt->fetch() ?: null;
    }
}