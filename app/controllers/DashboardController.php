<?php

namespace App\Controllers;

use App\Models\ReporteModel;
use App\Models\ConfiguracionModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class DashboardController extends Controller
{
    /** @var ReporteModel */
    private $reportes;
    /** @var ConfiguracionModel */
    private $configuracion;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->reportes = new ReporteModel();
        $this->configuracion = new ConfiguracionModel();
    }

    public function index()
    {
        $usuario = Session::get('user');
        $config = $usuario ? $this->configuracion->porColegio((int) ($usuario['id_colegio'] ?? 0)) : null;
        $topResponsablesLimite = (int) ($config['dashboard_top_responsables'] ?? 5);
        $mesesCartera = (int) ($config['dashboard_meses_cartera'] ?? 6);
        if ($topResponsablesLimite < 3) {
            $topResponsablesLimite = 3;
        } elseif ($topResponsablesLimite > 15) {
            $topResponsablesLimite = 15;
        }
        if ($mesesCartera < 3) {
            $mesesCartera = 3;
        } elseif ($mesesCartera > 12) {
            $mesesCartera = 12;
        }

        $kpis = [
            'carteraPendiente' => $this->reportes->carteraPendiente(),
            'pagosUltimoMes' => $this->reportes->totalPagosUltimoMes(),
            'topResponsables' => $this->reportes->topResponsables($topResponsablesLimite),
            'totalResponsables' => $this->reportes->totalResponsables(),
            'carteraMeses' => $this->reportes->carteraUltimosMeses($mesesCartera),
            'recaudoMeses' => $this->reportes->recaudoUltimosMeses($mesesCartera),
            'dashboardTopLimite' => $topResponsablesLimite,
            'dashboardMeses' => $mesesCartera,
        ];

        $this->view('dashboard/index', $kpis);
    }
}
