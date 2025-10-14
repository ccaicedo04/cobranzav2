<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\EstudianteModel;
use App\Models\PagoModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class PagoController extends Controller
{
    private PagoModel $pagos;
    private EstudianteModel $estudiantes;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->pagos = new PagoModel();
        $this->estudiantes = new EstudianteModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $pagos = $this->pagos->listadoCompleto();
        $this->view('pagos/index', [
            'pagos' => $pagos,
        ]);
    }

    public function create(): void
    {
        $this->view('pagos/form', [
            'estudiantes' => $this->estudiantes->all(),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=pagos');
        }

        $usuario = Session::get('user');
        $data = [
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'id_estudiante' => $_POST['id_estudiante'] ?? null,
            'fecha_pago' => $_POST['fecha_pago'] ?? date('Y-m-d'),
            'valor_total' => $_POST['valor_total'] ?? 0,
            'metodo_pago' => $_POST['metodo_pago'] ?? 'efectivo',
            'referencia' => $_POST['referencia'] ?? '',
            'observaciones' => $_POST['observaciones'] ?? null,
            'ruta_soporte' => null,
            'eliminado' => 0,
        ];

        $this->pagos->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'pagos',
            'accion' => 'registrar',
            'detalle' => 'Pago registrado para estudiante ' . $data['id_estudiante'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=pagos');
    }
}
