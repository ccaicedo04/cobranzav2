<?php

namespace App\Models;

class ParametroModel extends BaseModel
{
    /** @var string */
    protected $table = 'parametros_sistema';
    /** @var string */
    protected $primaryKey = 'id_parametro';
    /** @var array */
    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'id_colegio',
        'id_sede',
        'eliminado',
    ];
}
