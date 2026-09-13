<?php
declare(strict_types=1);

final class Role extends BaseModel
{
    protected string $table = 'roles';

    public function allWithPermissionCount(): array
    {
        return $this->db->query(
            "SELECT r.*, COUNT(rp.permission_id) AS permission_count
             FROM roles r
             LEFT JOIN role_permissions rp ON rp.role_id = r.id
             GROUP BY r.id ORDER BY r.id"
        )->fetchAll();
    }
}