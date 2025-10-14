<?php

namespace App\Models;

class ResponsableModel extends BaseModel
{
    protected string $table = 'responsable_financiero';
    protected string $primaryKey = 'id_responsable';
    protected array $fillable = [
        'id_colegio',
        'id_sede',
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'correo',
        'direccion',
        'estado',
        'eliminado',
    ];

    public function conContexto(array $filtros = []): array
    {
        $filtros = $this->applyTenantFilters($filtros);
        $where = ['r.eliminado = 0'];
        $params = [];

        foreach ($filtros as $column => $valor) {
            if ($valor === null || $valor === '') {
                continue;
            }

            $where[] = "r.$column = :$column";
            $params[":$column"] = $valor;
        }

        $sql = 'SELECT r.*, c.nombre AS colegio_nombre, s.nombre AS sede_nombre'
            . ' FROM responsable_financiero r'
            . ' INNER JOIN colegio c ON c.id_colegio = r.id_colegio'
            . ' INNER JOIN sede s ON s.id_sede = r.id_sede';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY c.nombre, s.nombre, r.nombre_completo';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
