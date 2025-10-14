<?php

namespace App\Models;

class CargaMasivaModel extends BaseModel
{
    protected string $table = 'carga_masiva';
    protected string $primaryKey = 'id_carga';
    protected bool $softDelete = false;
    protected array $fillable = [
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
