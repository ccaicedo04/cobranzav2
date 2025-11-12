<?php

namespace App\Models;

class ComunicacionAdjuntoModel extends BaseModel
{
    protected string $table = 'comunicacion_adjunto';
    protected string $primaryKey = 'id_adjunto';
    protected bool $softDelete = false;
    protected array $fillable = [
        'id_comunicacion',
        'nombre',
        'ruta',
        'tipo',
        'tamano',
        'metadata',
    ];

    public function porComunicaciones(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id_comunicacion IN (' . $placeholders . ') ORDER BY id_adjunto ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);

        return $stmt->fetchAll();
    }
}
