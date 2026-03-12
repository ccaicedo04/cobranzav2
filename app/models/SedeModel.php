<?php

namespace App\Models;

class SedeModel extends BaseModel
{
    protected string $table = 'sede';
    protected string $primaryKey = 'id_sede';
    protected array $fillable = [
        'id_colegio',
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'estado',
        'eliminado',
    ];

    protected array $tenantColumns = ['id_colegio'];

    public function conColegio(array $filters = []): array
    {
        $filters = $this->applyTenantFilters($filters);
        [$where, $params] = $this->compileFilters($filters, 's');
        $where[] = 's.eliminado = 0';

        $sql = 'SELECT s.*, c.nombre AS colegio_nombre, c.nit AS colegio_nit'
            . ' FROM sede s'
            . ' INNER JOIN colegio c ON c.id_colegio = s.id_colegio';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY c.nombre, s.nombre';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function porIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn ($id) => $id !== null && $id !== '')));
        if (!$ids) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = 'SELECT s.*, c.nombre AS colegio_nombre FROM sede s INNER JOIN colegio c ON c.id_colegio = s.id_colegio'
            . ' WHERE s.id_sede IN (' . $placeholders . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }
}
