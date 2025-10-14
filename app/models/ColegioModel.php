<?php

namespace App\Models;

class ColegioModel extends BaseModel
{
    protected string $table = 'colegio';
    protected string $primaryKey = 'id_colegio';
    protected array $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'correo',
        'logo',
        'estado',
        'eliminado',
    ];

    protected array $tenantColumns = [];

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
