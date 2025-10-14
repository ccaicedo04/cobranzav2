<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\EstudianteModel;
use App\Models\PeriodoModel;
use App\Models\ResponsableModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class EstudianteController extends Controller
{
    private EstudianteModel $estudiantes;
    private ResponsableModel $responsables;
    private PeriodoModel $periodos;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->estudiantes = new EstudianteModel();
        $this->responsables = new ResponsableModel();
        $this->periodos = new PeriodoModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $filtros = [
            'estado' => $_GET['estado'] ?? null,
        ];

        $estudiantes = $this->estudiantes->all(array_filter($filtros));
        $this->view('estudiantes/index', [
            'estudiantes' => $estudiantes,
            'filtros' => $filtros,
        ]);
    }

    public function create(): void
    {
        $responsables = $this->responsables->all();
        $this->view('estudiantes/form', [
            'responsables' => $responsables,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $usuario = Session::get('user');
        $data = [
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $_POST['id_sede'] ?? $usuario['id_sede'],
            'id_responsable' => $_POST['id_responsable'] ?? null,
            'codigo_estudiante' => $_POST['codigo_estudiante'] ?? '',
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'grado' => $_POST['grado'] ?? '',
            'curso' => $_POST['curso'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo',
            'eliminado' => 0,
        ];

        $id = $this->estudiantes->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'estudiantes',
            'accion' => 'crear',
            'detalle' => 'Creación de estudiante ' . $data['nombre_completo'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=estudiantes/detalle&id=' . $id);
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $estudiante = $this->estudiantes->find($id);
        if (!$estudiante) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $responsables = $this->responsables->all();
        $this->view('estudiantes/form', [
            'estudiante' => $estudiante,
            'responsables' => $responsables,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $id = (int) ($_POST['id_estudiante'] ?? 0);
        $usuario = Session::get('user');
        $data = [
            'id_sede' => $_POST['id_sede'] ?? $usuario['id_sede'],
            'id_responsable' => $_POST['id_responsable'] ?? null,
            'codigo_estudiante' => $_POST['codigo_estudiante'] ?? '',
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'grado' => $_POST['grado'] ?? '',
            'curso' => $_POST['curso'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $this->estudiantes->update($id, $data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'estudiantes',
            'accion' => 'actualizar',
            'detalle' => 'Actualización de estudiante ID ' . $id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=estudiantes/detalle&id=' . $id);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $usuario = Session::get('user');
        $this->estudiantes->delete($id);

        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'estudiantes',
            'accion' => 'eliminar',
            'detalle' => 'Eliminación de estudiante ID ' . $id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=estudiantes');
    }

    public function detalle(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $estudiante = $this->estudiantes->find($id);
        if (!$estudiante) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $this->view('estudiantes/detalle', [
            'estudiante' => $estudiante,
        ]);
    }
}
