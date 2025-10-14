<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ColegioController extends Controller
{
    private ColegioModel $colegios;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->colegios = new ColegioModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $this->requireRole('admin_global');
        $colegios = $this->colegios->all([]);
        $this->view('administracion/colegios/index', [
            'colegios' => $colegios,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin_global');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=colegios');
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'nit' => $_POST['nit'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'logo' => null,
            'estado' => $_POST['estado'] ?? 'activo',
            'eliminado' => 0,
        ];
        $id = $this->colegios->create($data);
        $usuario = Session::get('user');
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'colegios',
            'accion' => 'crear',
            'detalle' => 'Colegio ' . $id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=colegios');
    }
}
