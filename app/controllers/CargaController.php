<?php

namespace App\Controllers;

use App\Models\CargaMasivaModel;
use App\Models\ComunicacionModel;
use App\Models\ReporteModel;
use App\Models\SedeModel;
use App\Models\ColegioModel;
use App\Services\CargaPhidiasService;
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
    /** @var CargaPhidiasService */
    private $cargador;
    /** @var SedeModel */
    private $sedes;
    /** @var ColegioModel */
    private $colegios;

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
        $this->cargador = new CargaPhidiasService();
        $this->sedes = new SedeModel();
        $this->colegios = new ColegioModel();
    }

    public function index()
    {
        $cargas = $this->cargas->all([], ['order' => 'fecha_registro DESC']);

        $this->view('carga_masiva/index', [
            'cargas' => $cargas,
            'ventana' => $this->resumenVentana($cargas),
            'token' => Helpers::csrfToken(),
            'colegios' => $this->colegiosDisponibles(),
            'sedes' => $this->sedesDisponibles(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $idColegio = (int) ($_POST['id_colegio'] ?? ($tenant['id_colegio'] ?? 0));
        $idSede = (int) ($_POST['id_sede'] ?? ($tenant['id_sede'] ?? 0));
        $anio = (int) ($_POST['anio'] ?? date('Y'));
        $notas = trim((string) ($_POST['notas'] ?? ''));

        if ($idColegio <= 0 || $idSede <= 0) {
            Helpers::redirect('index.php?route=carga-masiva');
        }

        if (!isset($_FILES['archivo']) || ($_FILES['archivo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $archivo = $_FILES['archivo'];
        $ext = strtolower(pathinfo($archivo['name'] ?? 'carga.xlsx', PATHINFO_EXTENSION));
        if ($ext !== 'xlsx' || ($archivo['size'] ?? 0) > 10 * 1024 * 1024) {
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $cargaId = $this->cargas->create([
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
            'tipo_archivo' => 'xlsx',
            'archivo_original' => $archivo['name'] ?? 'carga.xlsx',
            'archivo_procesado' => null,
            'total_registros' => 0,
            'total_errores' => 0,
            'resultado' => 'Pendiente',
            'mensaje' => $notas !== '' ? $notas : 'Carga recibida',
            'usuario_registro' => $usuario['id_usuario'],
        ]);

        try {
            $resultado = $this->cargador->procesar($archivo['tmp_name'], $idColegio, $idSede, $anio);
            $errores = $resultado['errores'];
            $mensaje = $notas !== '' ? $notas : 'Carga procesada correctamente';
            $mensaje .= ' | Deudas creadas/actualizadas: ' . number_format((int) ($resultado['deudas'] ?? 0), 0, ',', '.');
            $mensaje .= ' | Valor total: $' . number_format((float) ($resultado['valor_total'] ?? 0), 0, ',', '.');
            if ($errores) {
                $mensaje .= ' | Errores: ' . count($errores);
            }

            $this->cargas->update($cargaId, [
                'total_registros' => (float) ($resultado['valor_total'] ?? 0),
                'total_errores' => count($errores),
                'resultado' => $errores ? 'Parcial' : 'Exitoso',
                'mensaje' => $mensaje,
            ]);
        } catch (\Throwable $exception) {
            $this->cargas->update($cargaId, [
                'total_registros' => 0,
                'total_errores' => 1,
                'resultado' => 'Error',
                'mensaje' => $exception->getMessage(),
            ]);
        }

        Helpers::redirect('index.php?route=carga-masiva');
    }

    private function resumenVentana(array $cargas): array
    {
        $ultimaCarga = $cargas[0] ?? null;
        $totalRegistros = 0.0;
        $totalErrores = 0;
        foreach ($cargas as $carga) {
            $totalRegistros += (float) ($carga['total_registros'] ?? 0);
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

    private function colegiosDisponibles(): array
    {
        $usuario = Session::get('user');
        if ($usuario['rol'] === 'admin_global') {
            return $this->colegios->all(['eliminado' => 0], ['order' => 'nombre']);
        }

        return $this->colegios->all([
            'id_colegio' => $usuario['id_colegio'] ?? null,
            'eliminado' => 0,
        ], ['order' => 'nombre']);
    }

    private function sedesDisponibles(): array
    {
        $usuario = Session::get('user');
        $filtro = ['eliminado' => 0];
        if ($usuario['rol'] !== 'admin_global' && !empty($usuario['id_colegio'])) {
            $filtro['id_colegio'] = $usuario['id_colegio'];
        }

        return $this->sedes->conColegio($filtro);
    }
}
