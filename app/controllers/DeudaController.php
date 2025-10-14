<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ConceptoModel;
use App\Models\DeudaModel;
use App\Models\EstudianteModel;
use App\Models\PeriodoModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class DeudaController extends Controller
{
    private DeudaModel $deudas;
    private EstudianteModel $estudiantes;
    private ConceptoModel $conceptos;
    private PeriodoModel $periodos;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->deudas = new DeudaModel();
        $this->estudiantes = new EstudianteModel();
        $this->conceptos = new ConceptoModel();
        $this->periodos = new PeriodoModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $deudas = $this->deudas->listadoCompleto();
        $this->view('deudas/index', [
            'deudas' => $deudas,
        ]);
    }

    public function create(): void
    {
        $this->view('deudas/form', [
            'estudiantes' => $this->estudiantes->all(),
            'conceptos' => $this->conceptos->all(),
            'periodos' => $this->periodos->all(),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=deudas');
        }

        $usuario = Session::get('user');
        $data = [
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'id_estudiante' => $_POST['id_estudiante'] ?? null,
            'id_concepto' => $_POST['id_concepto'] ?? null,
            'id_periodo' => $_POST['id_periodo'] ?? null,
            'fecha_generacion' => $_POST['fecha_generacion'] ?? date('Y-m-d'),
            'valor_inicial' => $_POST['valor_inicial'] ?? 0,
            'saldo_actual' => $_POST['saldo_actual'] ?? $_POST['valor_inicial'] ?? 0,
            'estado' => $_POST['estado'] ?? 'pendiente',
            'fecha_vencimiento' => $_POST['fecha_vencimiento'] ?? null,
            'notas' => $_POST['notas'] ?? null,
            'eliminado' => 0,
        ];

        $this->deudas->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'deudas',
            'accion' => 'crear',
            'detalle' => 'Registro de deuda para estudiante ' . $data['id_estudiante'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=deudas');
    }
}
