<?php

namespace App\Controllers;

use App\Models\ReporteModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;
use Core\SimplePdf;

class ReporteController extends Controller
{
    private ReporteModel $reportes;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('administracion');

        $this->reportes = new ReporteModel();
    }

    public function index(): void
    {
        $tipo = $this->tipoDesdeRequest();
        $filtros = $this->extraerFiltros();
        $config = $this->definicionesReporte()[$tipo];
        $datos = $this->obtenerDatosPorTipo($tipo, $filtros);

        $this->view('reportes/index', [
            'carteraPendiente' => $this->reportes->carteraPendiente(),
            'topResponsables' => $this->reportes->topResponsables(),
            'tipo' => $tipo,
            'filtros' => $filtros,
            'configReporte' => $config,
            'datosReporte' => $datos,
            'metodosPago' => $this->reportes->metodosPagoDisponibles(),
        ]);
    }

    public function exportExcel(): void
    {
        $tipo = $this->tipoDesdeRequest();
        $filtros = $this->extraerFiltros();
        $config = $this->definicionesReporte()[$tipo];
        $datos = $this->obtenerDatosPorTipo($tipo, $filtros);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_' . $tipo . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, array_column($config['columnas'], 'etiqueta'));
        foreach ($datos as $fila) {
            $row = [];
            foreach ($config['columnas'] as $columna) {
                $valor = $this->formatearValor($fila[$columna['campo']] ?? '', $columna);
                $row[] = $valor;
            }
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function exportPdf(): void
    {
        $tipo = $this->tipoDesdeRequest();
        $filtros = $this->extraerFiltros();
        $config = $this->definicionesReporte()[$tipo];
        $datos = $this->obtenerDatosPorTipo($tipo, $filtros);

        $lineas = $this->construirLineasPdf($config, $datos, $filtros, $tipo);
        SimplePdf::download('reporte_' . $tipo . '.pdf', $lineas);
    }

    private function obtenerDatosPorTipo(string $tipo, array $filtros): array
    {
        return match ($tipo) {
            'pagos' => $this->reportes->reportePagos($filtros),
            'acuerdos' => $this->reportes->reporteAcuerdos($filtros),
            default => $this->reportes->reporteCartera($filtros),
        };
    }

    private function tipoDesdeRequest(): string
    {
        $tipo = $_GET['tipo'] ?? 'cartera';
        $tipo = is_string($tipo) ? strtolower($tipo) : 'cartera';
        $permitidos = array_keys($this->definicionesReporte());
        if (!in_array($tipo, $permitidos, true)) {
            $tipo = 'cartera';
        }

        return $tipo;
    }

    private function extraerFiltros(): array
    {
        $filtros = [
            'desde' => trim((string) ($_GET['desde'] ?? '')),
            'hasta' => trim((string) ($_GET['hasta'] ?? '')),
            'estado' => trim((string) ($_GET['estado'] ?? '')),
            'metodo' => trim((string) ($_GET['metodo'] ?? '')),
        ];

        if ($filtros['desde'] !== '' && $filtros['hasta'] !== '' && $filtros['desde'] > $filtros['hasta']) {
            [$filtros['desde'], $filtros['hasta']] = [$filtros['hasta'], $filtros['desde']];
        }

        return $filtros;
    }

    private function definicionesReporte(): array
    {
        return [
            'cartera' => [
                'titulo' => 'Cartera académica',
                'descripcion' => 'Detalle de las obligaciones activas por responsable y estudiante.',
                'columnas' => [
                    ['campo' => 'fecha_generacion', 'etiqueta' => 'Fecha', 'formato' => 'date', 'ancho' => 11],
                    ['campo' => 'estudiante', 'etiqueta' => 'Estudiante', 'ancho' => 20],
                    ['campo' => 'responsable', 'etiqueta' => 'Responsable', 'ancho' => 20],
                    ['campo' => 'concepto', 'etiqueta' => 'Concepto', 'ancho' => 18],
                    ['campo' => 'saldo_actual', 'etiqueta' => 'Saldo', 'formato' => 'money', 'prefijo' => '$ ', 'ancho' => 12],
                    ['campo' => 'estado', 'etiqueta' => 'Estado', 'ancho' => 10],
                ],
            ],
            'pagos' => [
                'titulo' => 'Pagos recibidos',
                'descripcion' => 'Comprobantes y registros de recaudo por sede y estudiante.',
                'columnas' => [
                    ['campo' => 'fecha_pago', 'etiqueta' => 'Fecha', 'formato' => 'date', 'ancho' => 11],
                    ['campo' => 'estudiante', 'etiqueta' => 'Estudiante', 'ancho' => 18],
                    ['campo' => 'responsable', 'etiqueta' => 'Responsable', 'ancho' => 18],
                    ['campo' => 'metodo_pago', 'etiqueta' => 'Método', 'ancho' => 12],
                    ['campo' => 'valor_total', 'etiqueta' => 'Valor', 'formato' => 'money', 'prefijo' => '$ ', 'ancho' => 12],
                    ['campo' => 'referencia', 'etiqueta' => 'Referencia', 'ancho' => 12],
                ],
            ],
            'acuerdos' => [
                'titulo' => 'Acuerdos de pago',
                'descripcion' => 'Estado de los compromisos de pago y seguimiento de cuotas.',
                'columnas' => [
                    ['campo' => 'fecha_inicio', 'etiqueta' => 'Inicio', 'formato' => 'date', 'ancho' => 11],
                    ['campo' => 'responsable', 'etiqueta' => 'Responsable', 'ancho' => 20],
                    ['campo' => 'estudiante', 'etiqueta' => 'Estudiante', 'ancho' => 20],
                    ['campo' => 'monto_total', 'etiqueta' => 'Monto', 'formato' => 'money', 'prefijo' => '$ ', 'ancho' => 12],
                    ['campo' => 'cuotas', 'etiqueta' => 'Cuotas', 'formato' => 'int', 'ancho' => 8],
                    ['campo' => 'estado', 'etiqueta' => 'Estado', 'ancho' => 12],
                ],
            ],
        ];
    }

    private function formatearValor(mixed $valor, array $columna): string
    {
        $formato = $columna['formato'] ?? null;
        $prefijo = $columna['prefijo'] ?? '';
        $sufijo = $columna['sufijo'] ?? '';

        if ($formato === 'money') {
            $valor = number_format((float) $valor, 0, ',', '.');
        } elseif ($formato === 'int') {
            $valor = (string) (int) $valor;
        } elseif ($formato === 'date' && !empty($valor)) {
            $timestamp = strtotime((string) $valor);
            $valor = $timestamp ? date('Y-m-d', $timestamp) : (string) $valor;
        }

        if ($valor === null || $valor === '') {
            return '';
        }

        return $prefijo . (string) $valor . $sufijo;
    }

    private function construirLineasPdf(array $config, array $datos, array $filtros, string $tipo): array
    {
        $lineas = [];
        $lineas[] = strtoupper($config['titulo']);
        if (!empty($config['descripcion'])) {
            $lineas[] = $config['descripcion'];
        }
        $lineas[] = 'Generado: ' . date('Y-m-d H:i');
        $lineas[] = 'Registros: ' . count($datos);

        $resumenFiltros = [];
        if (!empty($filtros['desde'])) {
            $resumenFiltros[] = 'Desde ' . $filtros['desde'];
        }
        if (!empty($filtros['hasta'])) {
            $resumenFiltros[] = 'Hasta ' . $filtros['hasta'];
        }
        if ($tipo === 'pagos' && !empty($filtros['metodo'])) {
            $resumenFiltros[] = 'Método ' . strtoupper($filtros['metodo']);
        }
        if ($tipo !== 'pagos' && !empty($filtros['estado'])) {
            $resumenFiltros[] = 'Estado ' . strtoupper($filtros['estado']);
        }
        if ($resumenFiltros) {
            $lineas[] = implode(' | ', $resumenFiltros);
        }

        $lineas[] = str_repeat('-', $this->anchoTabla($config['columnas']));
        $encabezado = [];
        foreach ($config['columnas'] as $columna) {
            $encabezado[] = $this->ajustarAnchura($columna['etiqueta'], $columna['ancho']);
        }
        $lineas[] = implode(' ', $encabezado);
        $lineas[] = str_repeat('-', $this->anchoTabla($config['columnas']));

        if (!$datos) {
            $lineas[] = 'No hay información para los filtros aplicados.';
        } else {
            foreach ($datos as $fila) {
                $row = [];
                foreach ($config['columnas'] as $columna) {
                    $valor = $this->formatearValor($fila[$columna['campo']] ?? '', $columna);
                    $row[] = $this->ajustarAnchura($valor, $columna['ancho']);
                }
                $lineas[] = implode(' ', $row);
            }
        }

        $lineas[] = str_repeat('-', $this->anchoTabla($config['columnas']));
        $lineas[] = 'Desarrollado por: Technology and Innovation';

        return $lineas;
    }

    private function ajustarAnchura(string $texto, int $ancho): string
    {
        if ($ancho <= 0) {
            return '';
        }

        $truncado = mb_strimwidth($texto, 0, $ancho, '…', 'UTF-8');
        $longitud = mb_strwidth($truncado, 'UTF-8');
        if ($longitud < $ancho) {
            $truncado .= str_repeat(' ', $ancho - $longitud);
        }

        return $truncado;
    }

    private function anchoTabla(array $columnas): int
    {
        $ancho = 0;
        $conteo = count($columnas);
        foreach ($columnas as $columna) {
            $ancho += (int) ($columna['ancho'] ?? 0);
        }

        return $ancho + max($conteo - 1, 0);
    }
}
