<?php

namespace App\Models;

class SedeModel extends BaseModel
{
    protected string $table = 'sede';
    protected string $primaryKey = 'id_sede';
    protected array $fillable = [
        'id_colegio',
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'estado',
        'eliminado',
    ];

    protected array $tenantColumns = ['id_colegio'];
}
