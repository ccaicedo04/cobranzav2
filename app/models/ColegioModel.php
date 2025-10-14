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
}
