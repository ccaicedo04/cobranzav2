<?php

namespace App\Models;

class ModuloModel extends BaseModel
{
    /** @var string */
    protected $table = 'modulo_sistema';
    /** @var string */
    protected $primaryKey = 'id_modulo';
    /** @var bool */
    protected $softDelete = false;
    /** @var array */
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
    ];

    public function activos(): array
    {
        return $this->all(['estado' => 'activo'], ['order' => 'nombre']);
    }

    public function mapearPorCodigo(array $codigos): array
    {
        $codigos = array_values(array_unique(array_filter($codigos, static fn ($codigo) => $codigo !== null && $codigo !== '')));
        if (!$codigos) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($codigos), '?'));
        $sql = 'SELECT * FROM modulo_sistema WHERE codigo IN (' . $placeholders . ') AND estado = "activo"';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($codigos);

        return $stmt->fetchAll();
    }
}
