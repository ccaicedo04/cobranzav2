<?php

namespace App\Models;

class ConfiguracionModel extends BaseModel
{
    protected string $table = 'configuracion_colegio';
    protected string $primaryKey = 'id_configuracion';
    protected bool $softDelete = false;
    protected array $fillable = [
        'id_colegio',
        'smtp_host',
        'smtp_puerto',
        'smtp_usuario',
        'smtp_password',
        'whatsapp_api_key',
        'whatsapp_endpoint',
        'sms_api_key',
        'sms_endpoint',
        'logo_path',
        'actualizado_por',
        'fecha_actualizacion',
    ];
}
