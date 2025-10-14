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
}
