<?php

namespace App\Controllers;

use App\Models\ReporteModel;
use Core\Controller;
use Core\Session;
use Core\SimplePdf;

class ReporteController extends Controller
{
    private ReporteModel $reportes;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            \Core\Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('administracion');

        $this->reportes = new ReporteModel();
    }

    public function index(): void
    {
        $this->view('reportes/index', [
            'carteraPendiente' => $this->reportes->carteraPendiente(),
            'topResponsables' => $this->reportes->topResponsables(),
        ]);
    }

    public function exportExcel(): void
    {
        $datos = $this->reportes->topResponsables(20);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_top_responsables.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Responsable', 'Total']);
        foreach ($datos as $fila) {
            fputcsv($output, [$fila['nombre_completo'], $fila['total']]);
        }
        fclose($output);
        exit;
    }

    public function exportPdf(): void
    {
        $datos = $this->reportes->topResponsables(20);
        $lineas = [
            'Reporte de top responsables de cartera',
            'Generado: ' . date('Y-m-d H:i'),
            '',
        ];
        if ($datos) {
            foreach ($datos as $fila) {
                $lineas[] = $fila['nombre_completo'] . ' - $' . number_format((float) $fila['total'], 0, ',', '.');
            }
        } else {
            $lineas[] = 'No hay información disponible para el periodo consultado.';
        }

        SimplePdf::download('reporte_top_responsables.pdf', $lineas);
    }
}
