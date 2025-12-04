<?php

namespace App\Models;

class CargaMasivaModel extends BaseModel
{
    /** @var string */
    protected $table = 'carga_masiva';
    /** @var string */
    protected $primaryKey = 'id_carga';
    /** @var bool */
    protected $softDelete = false;
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'id_sede',
        'tipo_archivo',
        'archivo_original',
        'archivo_procesado',
        'total_registros',
        'total_errores',
        'resultado',
        'mensaje',
        'usuario_registro',
    ];
}
