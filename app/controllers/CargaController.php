<?php

namespace App\Controllers;

use App\Models\CargaMasivaModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class CargaController extends Controller
{
    private CargaMasivaModel $cargas;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->cargas = new CargaMasivaModel();
    }

    public function index(): void
    {
        $this->view('carga_masiva/index', [
            'cargas' => $this->cargas->all([], ['order' => 'fecha_registro DESC']),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $usuario = Session::get('user');
        $this->cargas->create([
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'tipo_archivo' => 'xlsx',
            'archivo_original' => $_FILES['archivo']['name'] ?? 'carga.xlsx',
            'archivo_procesado' => null,
            'total_registros' => 0,
            'total_errores' => 0,
            'resultado' => 'Pendiente',
            'mensaje' => 'Carga simulada en entorno demo',
            'usuario_registro' => $usuario['id_usuario'],
        ]);

        Helpers::redirect('index.php?route=carga-masiva');
    }
}
