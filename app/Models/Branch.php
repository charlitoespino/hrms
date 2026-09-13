<?php
declare(strict_types=1);

final class Branch extends BaseModel
{
    protected string $table = 'branches';

    public function allActive(): array
    {
        return $this->db->query(
            'SELECT * FROM branches WHERE deleted_at IS NULL ORDER BY name ASC'
        )->fetchAll();
    }
}