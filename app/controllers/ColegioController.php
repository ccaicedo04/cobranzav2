<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\SedeModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ColegioController extends Controller
{
    /** @var ColegioModel */
    private $colegios;
    /** @var AuditoriaModel */
    private $auditoria;
    /** @var SedeModel */
    private $sedes;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('administracion');

        $this->colegios = new ColegioModel();
        $this->auditoria = new AuditoriaModel();
        $this->sedes = new SedeModel();
    }

    public function index()
    {
        $this->requireRole('admin_global');
        $colegios = $this->colegios->all([]);
        $this->view('administracion/colegios/index', [
            'colegios' => $colegios,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store()
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
            'detalle' => 'Creación de colegio: ' . ($data['nombre'] ?: ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=colegios');
    }

    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $colegio = $this->colegioAccesible($id);

        $this->view('administracion/colegios/form', [
            'colegio' => $colegio,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=colegios');
        }

        $id = (int) ($_POST['id_colegio'] ?? 0);
        if (!$id) {
            Helpers::redirect('index.php?route=colegios');
        }

        $colegio = $this->colegioAccesible($id);

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'nit' => $_POST['nit'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $this->colegios->update($id, $data);
        $usuario = Session::get('user');
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'colegios',
            'accion' => 'actualizar',
            'detalle' => 'Actualización de colegio: ' . ($data['nombre'] ?: ($colegio['nombre'] ?? ('ID ' . $id))),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=colegios');
    }

    public function detalle()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $colegio = $this->colegioAccesible($id);

        $sedes = $this->sedes->conColegio(['id_colegio' => $id]);

        $this->view('administracion/colegios/detalle', [
            'colegio' => $colegio,
            'sedes' => $sedes,
        ]);
    }

    private function colegioAccesible(int $id): array
    {
        if ($id <= 0) {
            Helpers::redirect('index.php?route=colegios');
        }

        $colegio = $this->colegios->find($id);
        if (!$colegio) {
            Helpers::redirect('index.php?route=colegios');
        }

        $usuario = Session::get('user');
        if ($usuario['rol'] === 'admin_global') {
            return $colegio;
        }

        if ($usuario['rol'] === 'admin_colegio' && (int) ($usuario['id_colegio'] ?? 0) === $id) {
            return $colegio;
        }

        Helpers::redirect('index.php?route=colegios');
        return $colegio;
    }
}
