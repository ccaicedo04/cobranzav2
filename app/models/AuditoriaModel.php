<?php

namespace App\Models;

class AuditoriaModel extends BaseModel
{
    protected string $table = 'auditoria_usuario';
    protected string $primaryKey = 'id_auditoria';
    protected bool $softDelete = false;
    protected array $fillable = [
        'id_usuario',
        'id_colegio',
        'id_sede',
        'modulo',
        'accion',
        'detalle',
        'ip',
        'fecha_registro',
    ];
}
