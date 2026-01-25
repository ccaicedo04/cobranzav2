<?php

namespace App\Models;

class ConfiguracionModel extends BaseModel
{
    /** @var string */
    protected $table = 'configuracion_colegio';
    /** @var string */
    protected $primaryKey = 'id_configuracion';
    /** @var bool */
    protected $softDelete = false;
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'smtp_host',
        'smtp_puerto',
        'smtp_usuario',
        'smtp_password',
        'whatsapp_api_key',
        'whatsapp_endpoint',
        'sms_api_key',
        'sms_endpoint',
        'twilio_account_sid',
        'twilio_auth_token',
        'twilio_whatsapp_from',
        'twilio_whatsapp_template_sid',
        'twilio_sms_from',
        'twilio_default_country',
        'twilio_status_callback',
        'twilio_incoming_webhook',
        'dashboard_top_responsables',
        'dashboard_meses_cartera',
        'logo_path',
        'actualizado_por',
        'fecha_actualizacion',
    ];

    /**
     * @return array|null
     */
    public function porColegio(int $idColegio)
    {
        $registros = $this->all(['id_colegio' => $idColegio], ['order' => 'id_configuracion DESC']);

        return $registros[0] ?? null;
    }

    /**
     * @return array|null
     */
    public function obtenerTwilio(int $idColegio)
    {
        $configuracion = $this->porColegio($idColegio);
        if (!$configuracion) {
            return null;
        }

        return [
            'account_sid' => $configuracion['twilio_account_sid'] ?? null,
            'auth_token' => $configuracion['twilio_auth_token'] ?? null,
            'whatsapp_from' => $configuracion['twilio_whatsapp_from'] ?? null,
            'whatsapp_template_sid' => $configuracion['twilio_whatsapp_template_sid'] ?? null,
            'sms_from' => $configuracion['twilio_sms_from'] ?? null,
            'default_country' => $configuracion['twilio_default_country'] ?? null,
            'status_callback' => $configuracion['twilio_status_callback'] ?? null,
        ];
    }
}
