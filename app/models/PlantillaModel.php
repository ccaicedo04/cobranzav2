<?php

namespace App\Models;

class PlantillaModel extends BaseModel
{
    /** @var string */
    protected $table = 'plantilla_comunicacion';
    /** @var string */
    protected $primaryKey = 'id_plantilla';
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'nombre',
        'canal',
        'descripcion',
        'asunto_default',
        'cuerpo_html',
        'variables',
        'estado',
        'eliminado',
        'creado_por',
        'actualizado_por',
        'fecha_actualizacion',
    ];

    public function activasPorCanal(?string $canal = null): array
    {
        $filtros = ['estado' => 'activo', 'eliminado' => 0];
        if ($canal) {
            $filtros['canal'] = $canal;
        }

        return $this->all($filtros, ['order' => 'nombre']);
    }
}
