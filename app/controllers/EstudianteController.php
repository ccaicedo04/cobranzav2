<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\DeudaModel;
use App\Models\EstudianteModel;
use App\Models\PagoModel;
use App\Models\PeriodoModel;
use App\Models\ResponsableModel;
use App\Models\SedeModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class EstudianteController extends Controller
{
    /** @var EstudianteModel */
    private $estudiantes;
    /** @var ResponsableModel */
    private $responsables;
    /** @var PeriodoModel */
    private $periodos;
    /** @var AuditoriaModel */
    private $auditoria;
    /** @var SedeModel */
    private $sedes;
    /** @var DeudaModel */
    private $deudas;
    /** @var PagoModel */
    private $pagos;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('cobranzas');

        $this->estudiantes = new EstudianteModel();
        $this->responsables = new ResponsableModel();
        $this->periodos = new PeriodoModel();
        $this->auditoria = new AuditoriaModel();
        $this->sedes = new SedeModel();
        $this->deudas = new DeudaModel();
        $this->pagos = new PagoModel();
    }

    public function index()
    {
        $filtros = [
            'estado' => $_GET['estado'] ?? null,
        ];

        $estudiantes = $this->estudiantes->conContexto(array_filter($filtros));
        $this->view('estudiantes/index', [
            'estudiantes' => $estudiantes,
            'filtros' => $filtros,
        ]);
    }

    public function create()
    {
        $responsables = $this->responsables->conContexto();
        $sedes = $this->sedesDisponibles();
        $this->view('estudiantes/form', [
            'responsables' => $responsables,
            'sedes' => $sedes,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $idColegio = $_POST['id_colegio'] ?? $tenant['id_colegio'];
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = $tenant['id_colegio'];
        }
        $data = [
            'id_colegio' => $idColegio,
            'id_sede' => $_POST['id_sede'] ?? $tenant['id_sede'],
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
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'modulo' => 'estudiantes',
            'accion' => 'crear',
            'detalle' => 'Creación de estudiante ' . $data['nombre_completo'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=estudiantes/detalle&id=' . $id);
    }

    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $datos = $this->estudiantes->conContexto(['id_estudiante' => $id]);
        $estudiante = $datos[0] ?? null;
        if (!$estudiante) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $responsables = $this->responsables->conContexto();
        $sedes = $this->sedesDisponibles();
        $this->view('estudiantes/form', [
            'estudiante' => $estudiante,
            'responsables' => $responsables,
            'sedes' => $sedes,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $id = (int) ($_POST['id_estudiante'] ?? 0);
        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $idColegio = $_POST['id_colegio'] ?? $tenant['id_colegio'];
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = $tenant['id_colegio'];
        }
        $data = [
            'id_colegio' => $idColegio,
            'id_sede' => $_POST['id_sede'] ?? $tenant['id_sede'],
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
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'modulo' => 'estudiantes',
            'accion' => 'actualizar',
            'detalle' => 'Actualización de estudiante: ' . ($data['nombre_completo'] ?: ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=estudiantes/detalle&id=' . $id);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $usuario = Session::get('user');
        $registro = $this->estudiantes->find($id);
        $this->estudiantes->delete($id);

        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'estudiantes',
            'accion' => 'eliminar',
            'detalle' => 'Eliminación de estudiante: ' . ($registro['nombre_completo'] ?? ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=estudiantes');
    }

    public function detalle()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $datos = $this->estudiantes->conContexto(['id_estudiante' => $id]);
        $estudiante = $datos[0] ?? null;
        if (!$estudiante) {
            Helpers::redirect('index.php?route=estudiantes');
        }

        $deudas = $this->deudas->porEstudiante($id);
        $pagos = $this->pagos->porEstudiante($id);

        $this->view('estudiantes/detalle', [
            'estudiante' => $estudiante,
            'deudas' => $deudas,
            'pagos' => $pagos,
        ]);
    }

    private function sedesDisponibles(): array
    {
        $usuario = Session::get('user');
        $filtro = [];
        if ($usuario['rol'] !== 'admin_global' && !empty($usuario['id_colegio'])) {
            $filtro['id_colegio'] = $usuario['id_colegio'];
        }

        return $this->sedes->conColegio($filtro);
    }
}
