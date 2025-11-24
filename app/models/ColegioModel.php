<?php

namespace App\Models;

class ColegioModel extends BaseModel
{
    /** @var string */
    protected $table = 'colegio';
    /** @var string */
    protected $primaryKey = 'id_colegio';
    /** @var array */
    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'correo',
        'logo',
        'estado',
        'eliminado',
    ];

    /** @var array */
    protected $tenantColumns = [];

    public function porIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn ($id) => $id !== null && $id !== '')));
        if (!$ids) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = 'SELECT * FROM colegio WHERE id_colegio IN (' . $placeholders . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }
}
