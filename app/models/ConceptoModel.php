<?php

namespace App\Models;

class ConceptoModel extends BaseModel
{
    protected string $table = 'concepto_deuda';
    protected string $primaryKey = 'id_concepto';
    protected array $fillable = [
        'id_colegio',
        'nombre',
        'descripcion',
        'tipo',
        'valor_base',
        'estado',
        'eliminado',
    ];

    public function conColegio(): array
    {
        $filters = $this->applyTenantFilters([]);
        $where = ['c.eliminado = 0'];
        $params = [];

        foreach ($filters as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $where[] = "c.$column = :$column";
            $params[":$column"] = $value;
        }

        $sql = 'SELECT c.*, col.nombre AS colegio_nombre'
            . ' FROM concepto_deuda c'
            . ' INNER JOIN colegio col ON col.id_colegio = c.id_colegio';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY col.nombre, c.nombre';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
