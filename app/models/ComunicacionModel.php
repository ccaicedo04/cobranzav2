<?php

namespace App\Models;

class ComunicacionModel extends BaseModel
{
    protected string $table = 'comunicacion';
    protected string $primaryKey = 'id_comunicacion';
    protected array $fillable = [
        'id_colegio',
        'id_sede',
        'id_responsable',
        'id_estudiante',
        'tipo',
        'canal',
        'asunto',
        'mensaje',
        'resultado',
        'fecha_envio',
        'usuario_registro',
        'eliminado',
    ];
}
