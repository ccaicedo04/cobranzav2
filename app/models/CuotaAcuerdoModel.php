<?php

namespace App\Models;

class CuotaAcuerdoModel extends BaseModel
{
    protected string $table = 'cuota_acuerdo';
    protected string $primaryKey = 'id_cuota';
    protected array $fillable = [
        'id_acuerdo',
        'numero_cuota',
        'fecha_pago',
        'valor_cuota',
        'estado',
        'fecha_pago_real',
        'observaciones',
    ];

    protected array $tenantColumns = [];
}
