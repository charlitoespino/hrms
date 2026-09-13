<?php
declare(strict_types=1);

final class Holiday extends BaseModel
{
    protected string $table = 'holidays';

    public function forYear(int $year): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM holidays WHERE YEAR(holiday_date) = :y ORDER BY holiday_date ASC'
        );
        $stmt->execute([':y' => $year]);
        return $stmt->fetchAll();
    }

    public function isHoliday(string $date): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM holidays WHERE holiday_date = :d AND is_active = 1 LIMIT 1'
        );
        $stmt->execute([':d' => $date]);
        return $stmt->fetch() ?: null;
    }

    public function allPaginated(int $page = 1, int $perPage = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM holidays ORDER BY holiday_date DESC LIMIT :lim OFFSET :off'
        );
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
        $total = (int) $this->db->query('SELECT COUNT(*) FROM holidays')->fetchColumn();
        return ['items' => $items, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }
}