<?php

namespace App\Controllers;

use App\Models\CargaMasivaModel;
use App\Models\ComunicacionModel;
use App\Models\ReporteModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class CargaController extends Controller
{
    /** @var CargaMasivaModel */
    private $cargas;
    /** @var ReporteModel */
    private $reportes;
    /** @var ComunicacionModel */
    private $comunicaciones;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('cobranzas');

        $this->cargas = new CargaMasivaModel();
        $this->reportes = new ReporteModel();
        $this->comunicaciones = new ComunicacionModel();
    }

    public function index(): void
    {
        $cargas = $this->cargas->all([], ['order' => 'fecha_registro DESC']);

        $this->view('carga_masiva/index', [
            'cargas' => $cargas,
            'ventana' => $this->resumenVentana($cargas),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $notas = trim((string) ($_POST['notas'] ?? ''));
        $this->cargas->create([
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'tipo_archivo' => 'xlsx',
            'archivo_original' => $_FILES['archivo']['name'] ?? 'carga.xlsx',
            'archivo_procesado' => null,
            'total_registros' => 0,
            'total_errores' => 0,
            'resultado' => 'Pendiente',
            'mensaje' => $notas !== '' ? $notas : 'Carga simulada en entorno demo',
            'usuario_registro' => $usuario['id_usuario'],
        ]);

        Helpers::redirect('index.php?route=carga-masiva');
    }

    private function resumenVentana(array $cargas): array
    {
        $ultimaCarga = $cargas[0] ?? null;
        $totalRegistros = 0;
        $totalErrores = 0;
        foreach ($cargas as $carga) {
            $totalRegistros += (int) ($carga['total_registros'] ?? 0);
            $totalErrores += (int) ($carga['total_errores'] ?? 0);
        }

        $inicioMes = date('Y-m-01 00:00:00');
        $finMes = date('Y-m-t 23:59:59');
        $gestionesMes = $this->comunicaciones->contarEntre($inicioMes, $finMes);
        $recaudoMes = $this->reportes->totalPagosUltimoMes();

        return [
            'ultima' => $ultimaCarga,
            'total_registros' => $totalRegistros,
            'total_errores' => $totalErrores,
            'gestiones_mes' => $gestionesMes,
            'recaudo_mes' => $recaudoMes,
        ];
    }
}
