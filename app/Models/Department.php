<?php
declare(strict_types=1);

final class Department extends BaseModel
{
    protected string $table = 'departments';

    public function allActive(): array
    {
        return $this->db->query(
            'SELECT * FROM departments WHERE deleted_at IS NULL ORDER BY name ASC'
        )->fetchAll();
    }

    public function withHeadcount(): array
    {
        return $this->db->query(
            "SELECT d.*, COUNT(e.id) AS headcount,
                    CONCAT(h.first_name,' ',h.last_name) AS head_name
             FROM departments d
             LEFT JOIN employees e ON e.department_id = d.id AND e.deleted_at IS NULL AND e.employment_status <> 'Separated'
             LEFT JOIN employees h ON h.id = d.head_employee_id
             WHERE d.deleted_at IS NULL
             GROUP BY d.id
             ORDER BY d.name ASC"
        )->fetchAll();
    }
}