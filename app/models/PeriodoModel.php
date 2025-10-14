<?php

namespace App\Models;

class PeriodoModel extends BaseModel
{
    protected string $table = 'periodo';
    protected string $primaryKey = 'id_periodo';
    protected array $fillable = [
        'id_colegio',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'eliminado',
    ];

    public function conColegio(): array
    {
        $filters = $this->applyTenantFilters([]);
        [$where, $params] = $this->compileFilters($filters, 'p');
        $where[] = 'p.eliminado = 0';

        $sql = 'SELECT p.*, c.nombre AS colegio_nombre'
            . ' FROM periodo p'
            . ' INNER JOIN colegio c ON c.id_colegio = p.id_colegio';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY c.nombre, p.nombre';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
