<?php

namespace App\Models;

class EstudianteModel extends BaseModel
{
    /** @var string */
    protected $table = 'estudiante';
    /** @var string */
    protected $primaryKey = 'id_estudiante';
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'id_sede',
        'id_responsable',
        'codigo_estudiante',
        'nombre_completo',
        'grado',
        'curso',
        'estado',
        'eliminado',
    ];

    public function conContexto(array $filtros = []): array
    {
        $filtros = $this->applyTenantFilters($filtros);
        [$where, $params] = $this->compileFilters($filtros, 'e');
        $where[] = 'e.eliminado = 0';

        $sql = 'SELECT e.*, c.nombre AS colegio_nombre, s.nombre AS sede_nombre, r.nombre_completo AS responsable_nombre'
            . ' FROM estudiante e'
            . ' INNER JOIN colegio c ON c.id_colegio = e.id_colegio'
            . ' INNER JOIN sede s ON s.id_sede = e.id_sede'
            . ' INNER JOIN responsable_financiero r ON r.id_responsable = e.id_responsable';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY c.nombre, s.nombre, e.nombre_completo';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function porResponsable(int $idResponsable): array
    {
        return $this->all(['id_responsable' => $idResponsable], ['order' => 'nombre_completo']);
    }
}
