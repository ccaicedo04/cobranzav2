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
        $this->requireModule('cobranzas');

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
        $responsableId = (int) ($_GET['responsable'] ?? 0);
        $filtros = [];
        if ($responsableId > 0) {
            $filtros['id_responsable'] = $responsableId;
        }
        $this->view('pagos/form', [
            'estudiantes' => $this->estudiantes->conContexto($filtros),
            'token' => Helpers::csrfToken(),
            'responsableId' => $responsableId,
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=pagos');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $estudianteId = (int) ($_POST['id_estudiante'] ?? 0);
        $estudiante = $estudianteId ? $this->estudiantes->find($estudianteId) : null;
        $data = [
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'id_estudiante' => $estudianteId ?: null,
            'fecha_pago' => $_POST['fecha_pago'] ?? date('Y-m-d'),
            'valor_total' => $_POST['valor_total'] ?? 0,
            'metodo_pago' => $_POST['metodo_pago'] ?? 'efectivo',
            'referencia' => $_POST['referencia'] ?? '',
            'observaciones' => $_POST['observaciones'] ?? null,
            'ruta_soporte' => null,
            'eliminado' => 0,
        ];

        if (!empty($_FILES['soporte']['name'])) {
            $ruta = $this->guardarSoporte($_FILES['soporte']);
            if ($ruta) {
                $data['ruta_soporte'] = $ruta;
            }
        }

        $idPago = $this->pagos->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'pagos',
            'accion' => 'registrar',
            'detalle' => 'Pago registrado #' . $idPago . ' para ' . ($estudiante['nombre_completo'] ?? ('Estudiante ID ' . $estudianteId)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        $responsableVolver = (int) ($_POST['responsable'] ?? 0);
        if ($responsableVolver > 0) {
            Helpers::redirect('index.php?route=responsables/detalle&id=' . $responsableVolver);
        }

        Helpers::redirect('index.php?route=pagos');
    }

    private function guardarSoporte(array $archivo): ?string
    {
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $permitidos = ['application/pdf', 'image/png', 'image/jpeg'];
        $tipo = $archivo['type'] ?? '';
        if (!in_array($tipo, $permitidos, true)) {
            return null;
        }

        $directorio = __DIR__ . '/../../public/uploads/comprobantes';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }

        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre = uniqid('comprobante_', true) . '.' . strtolower($extension);
        $destino = $directorio . '/' . $nombre;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            return null;
        }

        return 'uploads/comprobantes/' . $nombre;
    }
}
