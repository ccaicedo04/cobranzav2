<?php

namespace App\Models;

class ConceptoModel extends BaseModel
{
    /** @var string */
    protected $table = 'concepto_deuda';
    /** @var string */
    protected $primaryKey = 'id_concepto';
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'nombre',
        'descripcion',
        'tipo',
        'valor_base',
        'estado',
        'eliminado',
    ];

    /**
     * @return array|null
     */
    public function buscarPorNombre(string $nombre, int $idColegio)
    {
        $nombre = trim($nombre);
        if ($nombre === '') {
            return null;
        }

        $coincidencias = $this->all([
            'nombre' => $nombre,
            'id_colegio' => $idColegio,
            'eliminado' => 0,
        ], ['order' => 'id_concepto DESC']);

        return $coincidencias[0] ?? null;
    }

    public function conColegio(): array
    {
        $filters = $this->applyTenantFilters([]);
        [$where, $params] = $this->compileFilters($filters, 'c');
        $where[] = 'c.eliminado = 0';

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
