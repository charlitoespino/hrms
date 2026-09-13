<?php
declare(strict_types=1);

final class Position extends BaseModel
{
    protected string $table = 'positions';

    public function allWithDepartment(): array
    {
        return $this->db->query(
            "SELECT p.*, d.name AS department_name
             FROM positions p
             LEFT JOIN departments d ON d.id = p.department_id
             WHERE p.deleted_at IS NULL
             ORDER BY p.name ASC"
        )->fetchAll();
    }
}