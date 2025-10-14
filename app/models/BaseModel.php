<?php

namespace App\Models;

use Core\Database;
use Core\Session;
use PDO;

abstract class BaseModel
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $softDelete = true;
    protected array $fillable = [];
    protected array $tenantColumns = ['id_colegio', 'id_sede'];

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(array $filters = [], array $options = []): array
    {
        $filters = $this->applyTenantFilters($filters);
        $where = [];
        $params = [];

        foreach ($filters as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $where[] = "$column = :$column";
            $params[":$column"] = $value;
        }

        if ($this->softDelete && !isset($filters['eliminado'])) {
            $where[] = 'eliminado = 0';
        }

        $sql = 'SELECT * FROM ' . $this->table;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        if (isset($options['order'])) {
            $sql .= ' ORDER BY ' . $options['order'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id, array $filters = []): ?array
    {
        $filters[$this->primaryKey] = $id;
        $results = $this->all($filters);

        return $results[0] ?? null;
    }

    public function create(array $data): int
    {
        $payload = $this->filterFillable($data);
        $columns = array_keys($payload);
        $placeholders = array_map(fn ($col) => ':' . $col, $columns);

        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = $this->db->prepare($sql);

        $params = [];
        foreach ($payload as $column => $value) {
            $params[':' . $column] = $value;
        }

        $stmt->execute($params);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $payload = $this->filterFillable($data);
        $columns = array_keys($payload);
        $set = implode(',', array_map(fn ($col) => "$col = :$col", $columns));

        $sql = 'UPDATE ' . $this->table . ' SET ' . $set . ' WHERE ' . $this->primaryKey . ' = :id';
        $stmt = $this->db->prepare($sql);

        $params = [':id' => $id];
        foreach ($payload as $column => $value) {
            $params[':' . $column] = $value;
        }

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        if ($this->softDelete) {
            $sql = 'UPDATE ' . $this->table . ' SET eliminado = 1 WHERE ' . $this->primaryKey . ' = :id';
        } else {
            $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id';
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    protected function filterFillable(array $data): array
    {
        if (!$this->fillable) {
            return $data;
        }

        return array_filter(
            $data,
            fn ($key) => in_array($key, $this->fillable, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    protected function applyTenantFilters(array $filters): array
    {
        $user = Session::get('user');

        if (!$user) {
            return $filters;
        }

        foreach ($this->tenantColumns as $column) {
            if (!array_key_exists($column, $filters) && isset($user[$column])) {
                $filters[$column] = $user[$column];
            }
        }

        return $filters;
    }
}
