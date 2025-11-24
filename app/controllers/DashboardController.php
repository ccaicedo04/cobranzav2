<?php

namespace App\Controllers;

use App\Models\ReporteModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class DashboardController extends Controller
{
    /** @var ReporteModel */
    private $reportes;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->reportes = new ReporteModel();
    }

    public function index()
    {
        $kpis = [
            'carteraPendiente' => $this->reportes->carteraPendiente(),
            'pagosUltimoMes' => $this->reportes->totalPagosUltimoMes(),
            'topResponsables' => $this->reportes->topResponsables(),
            'carteraMeses' => $this->reportes->carteraUltimosMeses(),
            'recaudoMeses' => $this->reportes->recaudoUltimosMeses(),
        ];

        $this->view('dashboard/index', $kpis);
    }
}
