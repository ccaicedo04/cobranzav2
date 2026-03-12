<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ComunicacionModel;
use App\Models\EstudianteModel;
use App\Models\ResponsableModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ComunicacionController extends Controller
{
    private ComunicacionModel $comunicaciones;
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

        $this->comunicaciones = new ComunicacionModel();
        $this->responsables = new ResponsableModel();
        $this->estudiantes = new EstudianteModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $comunicaciones = $this->comunicaciones->all();
        $this->view('comunicaciones/index', [
            'comunicaciones' => $comunicaciones,
            'responsables' => $this->responsables->conContexto(),
            'estudiantes' => $this->estudiantes->conContexto(),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=comunicaciones');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $data = [
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'id_responsable' => $_POST['id_responsable'] ?? null,
            'id_estudiante' => $_POST['id_estudiante'] ?? null,
            'tipo' => $_POST['tipo'] ?? 'gestion',
            'canal' => $_POST['canal'] ?? 'whatsapp',
            'asunto' => $_POST['asunto'] ?? '',
            'mensaje' => $_POST['mensaje'] ?? '',
            'resultado' => $_POST['resultado'] ?? '',
            'fecha_envio' => date('Y-m-d H:i:s'),
            'usuario_registro' => $usuario['id_usuario'],
            'eliminado' => 0,
        ];

        $this->comunicaciones->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'modulo' => 'comunicaciones',
            'accion' => 'registrar',
            'detalle' => 'Registro de comunicación ' . $data['canal'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=comunicaciones');
    }
}
