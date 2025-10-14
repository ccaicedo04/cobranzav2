<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\SedeModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class SedeController extends Controller
{
    private SedeModel $sedes;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->sedes = new SedeModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $sedes = $this->sedes->all();
        $this->view('administracion/sedes/index', [
            'sedes' => $sedes,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=sedes');
        }

        $usuario = Session::get('user');
        $data = [
            'id_colegio' => $usuario['id_colegio'],
            'nombre' => $_POST['nombre'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo',
            'eliminado' => 0,
        ];
        $id = $this->sedes->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'sedes',
            'accion' => 'crear',
            'detalle' => 'Sede ' . $id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=sedes');
    }
}
