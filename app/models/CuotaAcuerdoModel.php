<?php

namespace App\Models;

class CuotaAcuerdoModel extends BaseModel
{
    /** @var string */
    protected $table = 'cuota_acuerdo';
    /** @var string */
    protected $primaryKey = 'id_cuota';
    /** @var array */
    protected $fillable = [
        'id_acuerdo',
        'numero_cuota',
        'fecha_pago',
        'valor_cuota',
        'estado',
        'fecha_pago_real',
        'observaciones',
    ];

    /** @var array */
    protected $tenantColumns = [];
}
