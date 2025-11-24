<?php

namespace App\Models;

class AcuerdoModel extends BaseModel
{
    /** @var string */
    protected $table = 'acuerdo_pago';
    /** @var string */
    protected $primaryKey = 'id_acuerdo';
    /** @var array */
    protected $fillable = [
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
