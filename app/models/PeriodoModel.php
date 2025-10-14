<?php

namespace App\Models;

class PeriodoModel extends BaseModel
{
    protected string $table = 'periodo';
    protected string $primaryKey = 'id_periodo';
    protected array $fillable = [
        'id_colegio',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'eliminado',
    ];
}
