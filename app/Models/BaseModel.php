<?php
declare(strict_types=1);

abstract class BaseModel
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(array $where = [], string $orderBy = 'id DESC', int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];
        if ($where) {
            $clauses = [];
            foreach ($where as $k => $v) {
                $clauses[] = "`$k` = :$k";
                $params[":$k"] = $v;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        $sql .= " ORDER BY $orderBy LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(array $where = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        $params = [];
        if ($where) {
            $clauses = [];
            foreach ($where as $k => $v) {
                $clauses[] = "`$k` = :$k";
                $params[":$k"] = $v;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function insert(array $data): int
    {
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ":$c", $cols);
        $sql = "INSERT INTO `{$this->table}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        foreach ($data as $k => $v) {
            $stmt->bindValue(":$k", $v);
        }
        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (!$data) return false;
        $sets = [];
        foreach (array_keys($data) as $c) {
            $sets[] = "`$c` = :$c";
        }
        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        foreach ($data as $k => $v) {
            $stmt->bindValue(":$k", $v);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}