<?php

namespace App\Models;

class ComunicacionModel extends BaseModel
{
    /** @var string */
    protected $table = 'comunicacion';
    /** @var string */
    protected $primaryKey = 'id_comunicacion';
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'id_sede',
        'id_responsable',
        'id_estudiante',
        'id_plantilla',
        'tipo',
        'canal',
        'asunto',
        'mensaje',
        'resultado',
        'fecha_envio',
        'usuario_registro',
        'estado_envio',
        'detalle_envio',
        'eliminado',
    ];

    public function contarEntre(string $desde, string $hasta): int
    {
        $filters = $this->applyTenantFilters([]);
        [$where, $params] = $this->compileFilters($filters);

        if ($this->softDelete && $this->softDeleteColumn && $this->tableHasColumn($this->softDeleteColumn)) {
            $where[] = $this->softDeleteColumn . ' = 0';
        }

        $where[] = 'fecha_envio BETWEEN :desde AND :hasta';
        $params[':desde'] = $desde;
        $params[':hasta'] = $hasta;

        $sql = 'SELECT COUNT(*) FROM ' . $this->table;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param string|null $canal
     */
    public function porResponsable(int $responsableId, $canal = null, int $limit = 0): array
    {
        $filters = $this->applyTenantFilters(['id_responsable' => $responsableId]);
        if ($canal) {
            $filters['canal'] = $canal;
        }

        [$where, $params] = $this->compileFilters($filters);
        if ($this->softDelete && $this->softDeleteColumn && $this->tableHasColumn($this->softDeleteColumn)) {
            $where[] = $this->softDeleteColumn . ' = 0';
        }

        $sql = 'SELECT * FROM ' . $this->table;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY fecha_envio ASC, id_comunicacion ASC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function ultimasEntrantes(int $limit = 5): array
    {
        $filters = $this->applyTenantFilters(['tipo' => 'inbound']);
        [$where, $params] = $this->compileFilters($filters);
        if ($this->softDelete && $this->softDeleteColumn && $this->tableHasColumn($this->softDeleteColumn)) {
            $where[] = $this->softDeleteColumn . ' = 0';
        }

        $sql = 'SELECT * FROM ' . $this->table;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY fecha_envio DESC, id_comunicacion DESC LIMIT ' . (int) max(1, $limit);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * @return array|null
     */
    public function ultimaEntradaPorResponsable(int $responsableId, string $canal)
    {
        $filters = $this->applyTenantFilters([
            'id_responsable' => $responsableId,
            'canal' => $canal,
            'tipo' => 'inbound',
        ]);

        [$where, $params] = $this->compileFilters($filters);
        if ($this->softDelete && $this->softDeleteColumn && $this->tableHasColumn($this->softDeleteColumn)) {
            $where[] = $this->softDeleteColumn . ' = 0';
        }

        $sql = 'SELECT * FROM ' . $this->table;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY fecha_envio DESC, id_comunicacion DESC LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $registro = $stmt->fetch();

        return $registro ?: null;
    }
}
