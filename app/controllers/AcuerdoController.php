<?php

namespace App\Controllers;

use App\Models\AcuerdoModel;
use App\Models\AuditoriaModel;
use App\Models\CuotaAcuerdoModel;
use App\Models\EstudianteModel;
use App\Models\ResponsableModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class AcuerdoController extends Controller
{
    private AcuerdoModel $acuerdos;
    private CuotaAcuerdoModel $cuotas;
    private ResponsableModel $responsables;
    private EstudianteModel $estudiantes;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('cobranzas');

        $this->acuerdos = new AcuerdoModel();
        $this->cuotas = new CuotaAcuerdoModel();
        $this->responsables = new ResponsableModel();
        $this->estudiantes = new EstudianteModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $responsableId = (int) ($_GET['responsable'] ?? 0);
        $acuerdos = $this->acuerdos->all();
        $responsablesListado = $this->responsables->conContexto();
        $estudiantesListado = $responsableId > 0
            ? $this->estudiantes->conContexto(['id_responsable' => $responsableId])
            : $this->estudiantes->conContexto();
        $this->view('acuerdos/index', [
            'acuerdos' => $acuerdos,
            'responsables' => $responsablesListado,
            'estudiantes' => $estudiantesListado,
            'token' => Helpers::csrfToken(),
            'responsableId' => $responsableId,
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=acuerdos');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $responsableId = (int) ($_POST['id_responsable'] ?? 0);
        $responsable = $responsableId ? $this->responsables->find($responsableId) : null;
        $acuerdoId = $this->acuerdos->create([
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'id_responsable' => $responsableId ?: null,
            'id_estudiante' => $_POST['id_estudiante'] ?? null,
            'monto_total' => $_POST['monto_total'] ?? 0,
            'cuotas' => $_POST['cuotas'] ?? 1,
            'fecha_inicio' => $_POST['fecha_inicio'] ?? date('Y-m-d'),
            'fecha_fin' => $_POST['fecha_fin'] ?? null,
            'estado' => $_POST['estado'] ?? 'activo',
            'observaciones' => $_POST['observaciones'] ?? null,
            'eliminado' => 0,
        ]);

        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'modulo' => 'acuerdos',
            'accion' => 'crear',
            'detalle' => 'Creación de acuerdo para ' . ($responsable['nombre_completo'] ?? ('ID ' . $responsableId)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        $responsableVolver = (int) ($_POST['responsable'] ?? 0);
        if ($responsableVolver > 0) {
            Helpers::redirect('index.php?route=responsables/detalle&id=' . $responsableVolver);
        }

        Helpers::redirect('index.php?route=acuerdos');
    }
}
