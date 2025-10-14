<?php

namespace App\Controllers;

use App\Models\ConfiguracionModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ConfiguracionController extends Controller
{
    private ConfiguracionModel $configuracion;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->configuracion = new ConfiguracionModel();
    }

    public function index(): void
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

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=configuracion');
        }

        $usuario = Session::get('user');
        $payload = [
            'id_colegio' => $usuario['id_colegio'],
            'smtp_host' => $_POST['smtp_host'] ?? '',
            'smtp_puerto' => $_POST['smtp_puerto'] ?? '',
            'smtp_usuario' => $_POST['smtp_usuario'] ?? '',
            'smtp_password' => $_POST['smtp_password'] ?? '',
            'whatsapp_api_key' => $_POST['whatsapp_api_key'] ?? '',
            'whatsapp_endpoint' => $_POST['whatsapp_endpoint'] ?? '',
            'sms_api_key' => $_POST['sms_api_key'] ?? '',
            'sms_endpoint' => $_POST['sms_endpoint'] ?? '',
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
