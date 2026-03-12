<?php

namespace App\Models;

class AcuerdoModel extends BaseModel
{
    protected string $table = 'acuerdo_pago';
    protected string $primaryKey = 'id_acuerdo';
    protected array $fillable = [
        'id_colegio',
        'id_sede',
        'id_responsable',
        'id_estudiante',
        'monto_total',
        'cuotas',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'observaciones',
        'eliminado',
    ];
}
