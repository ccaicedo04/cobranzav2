<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\EstudianteModel;
use App\Models\ResponsableModel;
use App\Models\SedeModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class SedeController extends Controller
{
    private SedeModel $sedes;
    private AuditoriaModel $auditoria;
    private ColegioModel $colegios;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('administracion');

        $this->sedes = new SedeModel();
        $this->auditoria = new AuditoriaModel();
        $this->colegios = new ColegioModel();
    }

    public function index(): void
    {
        $usuario = Session::get('user');
        $filtros = [];
        if (!empty($_GET['id_colegio'])) {
            $filtros['id_colegio'] = (int) $_GET['id_colegio'];
        }

        $sedes = $this->sedes->conColegio($filtros);
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        } elseif (!empty($usuario['id_colegio'])) {
            $colegios = $this->colegios->all(['id_colegio' => $usuario['id_colegio']]);
        }

        $this->view('administracion/sedes/index', [
            'sedes' => $sedes,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=sedes');
        }

        $usuario = Session::get('user');
        $idColegio = (int) ($_POST['id_colegio'] ?? 0);
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = (int) ($usuario['id_colegio'] ?? 0);
        }

        $data = [
            'id_colegio' => $idColegio,
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

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $sede = $this->sedes->find($id);
        if (!$sede) {
            Helpers::redirect('index.php?route=sedes');
        }

        $usuario = Session::get('user');
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        }

        $this->view('administracion/sedes/form', [
            'sede' => $sede,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=sedes');
        }

        $id = (int) ($_POST['id_sede'] ?? 0);
        if (!$id) {
            Helpers::redirect('index.php?route=sedes');
        }

        $usuario = Session::get('user');
        $idColegio = (int) ($_POST['id_colegio'] ?? 0);
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = (int) ($usuario['id_colegio'] ?? 0);
        }

        $data = [
            'id_colegio' => $idColegio,
            'nombre' => $_POST['nombre'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $this->sedes->update($id, $data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'sedes',
            'accion' => 'actualizar',
            'detalle' => 'Actualización sede ' . $id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=sedes');
    }

    public function detalle(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $detalle = $this->sedes->conColegio(['id_sede' => $id]);
        if (!$detalle) {
            Helpers::redirect('index.php?route=sedes');
        }

        $sede = $detalle[0];
        $responsables = (new ResponsableModel())->all(['id_sede' => $id]);
        $estudiantes = (new EstudianteModel())->all(['id_sede' => $id]);

        $this->view('administracion/sedes/detalle', [
            'sede' => $sede,
            'responsables' => $responsables,
            'estudiantes' => $estudiantes,
        ]);
    }
}
