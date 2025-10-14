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
    protected ?string $softDeleteColumn = 'eliminado';
    protected array $fillable = [];
    protected array $tenantColumns = ['id_colegio', 'id_sede'];
    protected static array $tableColumnsCache = [];

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

            if (is_array($value)) {
                $value = array_values(array_filter($value, static fn ($item) => $item !== null && $item !== ''));
                if (!$value) {
                    continue;
                }
                $placeholders = [];
                foreach ($value as $idx => $item) {
                    $placeholder = ':' . $column . '_' . $idx;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $item;
                }
                $where[] = $column . ' IN (' . implode(',', $placeholders) . ')';
            } else {
                $where[] = "$column = :$column";
                $params[":$column"] = $value;
            }
        }

        if (
            $this->softDelete
            && $this->softDeleteColumn
            && !isset($filters[$this->softDeleteColumn])
            && $this->tableHasColumn($this->softDeleteColumn)
        ) {
            $where[] = $this->softDeleteColumn . ' = 0';
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
        if ($this->softDelete && $this->softDeleteColumn && $this->tableHasColumn($this->softDeleteColumn)) {
            $sql = 'UPDATE ' . $this->table . ' SET ' . $this->softDeleteColumn . ' = 1 WHERE ' . $this->primaryKey . ' = :id';
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
        $context = Session::get('context');

        if (!$user) {
            return $filters;
        }

        foreach ($this->tenantColumns as $column) {
            if (array_key_exists($column, $filters)) {
                continue;
            }

            if ($column === 'id_colegio') {
                $seleccionado = $context['id_colegio'] ?? null;
                if ($seleccionado) {
                    $filters[$column] = (int) $seleccionado;
                    continue;
                }

                if (!empty($user['colegios_permitidos'])) {
                    $filters[$column] = array_map('intval', (array) $user['colegios_permitidos']);
                    continue;
                }
            }

            if ($column === 'id_sede') {
                $seleccionadoSede = $context['id_sede'] ?? null;
                if ($seleccionadoSede) {
                    $filters[$column] = (int) $seleccionadoSede;
                    continue;
                }

                if (!empty($user['sedes_permitidas'])) {
                    $filters[$column] = array_map('intval', (array) $user['sedes_permitidas']);
                    continue;
                }
            }

            if (isset($user[$column]) && $user[$column] !== null) {
                $filters[$column] = $user[$column];
            }
        }

        return $filters;
    }

    protected function tableHasColumn(string $column): bool
    {
        if (!$this->table) {
            return false;
        }

        if (!isset(self::$tableColumnsCache[$this->table])) {
            $stmt = $this->db->query('DESCRIBE ' . $this->table);
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            self::$tableColumnsCache[$this->table] = array_map(
                static fn (array $definition) => $definition['Field'] ?? '',
                $columns
            );
        }

        return in_array($column, self::$tableColumnsCache[$this->table], true);
    }
}
