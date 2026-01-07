<?php

namespace App\Controllers;

use App\Models\ConfiguracionModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ConfiguracionController extends Controller
{
    /** @var ConfiguracionModel */
    private $configuracion;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('parametrizacion');

        $this->configuracion = new ConfiguracionModel();
    }

    public function index()
    {
        $usuario = Session::get('user');
        $config = $this->configuracion->all([
            'id_colegio' => $usuario['id_colegio'],
        ]);
        $this->view('configuracion/index', [
            'configuracion' => $config[0] ?? null,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=configuracion');
        }

        $usuario = Session::get('user');
        $payload = [
            'id_colegio' => $usuario['id_colegio'],
            'smtp_host' => trim((string) ($_POST['smtp_host'] ?? '')),
            'smtp_puerto' => trim((string) ($_POST['smtp_puerto'] ?? '')),
            'smtp_usuario' => trim((string) ($_POST['smtp_usuario'] ?? '')),
            'smtp_password' => trim((string) ($_POST['smtp_password'] ?? '')),
            'whatsapp_api_key' => trim((string) ($_POST['whatsapp_api_key'] ?? '')),
            'whatsapp_endpoint' => trim((string) ($_POST['whatsapp_endpoint'] ?? '')),
            'sms_api_key' => trim((string) ($_POST['sms_api_key'] ?? '')),
            'sms_endpoint' => trim((string) ($_POST['sms_endpoint'] ?? '')),
            'twilio_account_sid' => trim((string) ($_POST['twilio_account_sid'] ?? '')),
            'twilio_auth_token' => trim((string) ($_POST['twilio_auth_token'] ?? '')),
            'twilio_whatsapp_from' => trim((string) ($_POST['twilio_whatsapp_from'] ?? '')),
            'twilio_whatsapp_template_sid' => trim((string) ($_POST['twilio_whatsapp_template_sid'] ?? '')),
            'twilio_sms_from' => trim((string) ($_POST['twilio_sms_from'] ?? '')),
            'twilio_default_country' => trim((string) ($_POST['twilio_default_country'] ?? '+57')) ?: '+57',
            'twilio_status_callback' => trim((string) ($_POST['twilio_status_callback'] ?? '')),
            'twilio_incoming_webhook' => trim((string) ($_POST['twilio_incoming_webhook'] ?? '')),
            'logo_path' => null,
            'actualizado_por' => $usuario['id_usuario'],
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
        ];

        $config = $this->configuracion->all(['id_colegio' => $usuario['id_colegio']]);
        if ($config) {
            $this->configuracion->update((int) $config[0]['id_configuracion'], $payload);
        } else {
            $this->configuracion->create($payload);
        }

        Helpers::redirect('index.php?route=configuracion');
    }
}
