<?php

namespace App\Models;

class ParametroModel extends BaseModel
{
    protected string $table = 'parametros_sistema';
    protected string $primaryKey = 'id_parametro';
    protected array $fillable = [
        'clave',
        'valor',
        'descripcion',
        'id_colegio',
        'id_sede',
        'eliminado',
    ];
}
