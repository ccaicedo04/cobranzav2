<?php

namespace App\Controllers;

use App\Models\ReporteModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;
use Core\SimplePdf;
use Core\SimpleXlsx;
use Throwable;

class ReporteController extends Controller
{
    /** @var ReporteModel */
    private $reportes;

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
        $headers = array_column($config['columnas'], 'etiqueta');
        $widths = array_map(static fn ($columna) => $columna['ancho'] ?? null, $config['columnas']);
        $rows = [];
        foreach ($datos as $fila) {
            $row = [];
            foreach ($config['columnas'] as $columna) {
                $row[] = $this->formatearValor($fila[$columna['campo']] ?? '', $columna);
            }
            $rows[] = $row;
        }

        try {
            SimpleXlsx::download('reporte_' . $tipo . '.xlsx', $headers, $rows, ['widths' => $widths]);
        } catch (Throwable $throwable) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'No fue posible generar el archivo XLSX: ' . $throwable->getMessage();
            exit;
        }
    }

    public function exportPdf(): void
    {
        $tipo = $this->tipoDesdeRequest();
        $filtros = $this->extraerFiltros();
        $config = $this->definicionesReporte()[$tipo];
        $datos = $this->obtenerDatosPorTipo($tipo, $filtros);

        $documento = $this->construirDocumentoPdf($config, $datos, $filtros, $tipo);
        SimplePdf::downloadTable('reporte_' . $tipo . '.pdf', $documento);
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

    private function construirDocumentoPdf(array $config, array $datos, array $filtros, string $tipo): array
    {
        $usuario = Session::get('user');
        $columnas = array_map(static function (array $columna): array {
            return [
                'campo' => $columna['campo'],
                'etiqueta' => $columna['etiqueta'],
                'ancho' => (float) ($columna['ancho'] ?? 20),
            ];
        }, $config['columnas']);

        $rows = [];
        foreach ($datos as $fila) {
            $rows[] = array_map(function (array $columna) use ($fila): string {
                $valor = $fila[$columna['campo']] ?? '';
                return $this->formatearValor($valor, $columna);
            }, $config['columnas']);
        }

        $contexto = $this->resumenContextoPdf();
        $meta = array_filter([
            'Generado' => date('Y-m-d H:i'),
            'Usuario' => $this->descripcionUsuario($usuario),
            'Colegio' => $contexto['colegio'],
            'Sede' => $contexto['sede'],
            'Registros' => number_format(count($rows), 0, ',', '.'),
        ], static fn ($valor) => $valor !== null && $valor !== '');

        return [
            'title' => $config['titulo'],
            'subtitle' => $config['descripcion'] ?? '',
            'columns' => $columnas,
            'rows' => $rows,
            'meta' => $meta,
            'filters' => $this->resumenFiltrosPdf($filtros, $tipo),
            'summary' => $this->totalesReporte($tipo, $datos),
        ];
    }

    private function resumenFiltrosPdf(array $filtros, string $tipo): array
    {
        $lineas = [];
        if (!empty($filtros['desde']) || !empty($filtros['hasta'])) {
            $rango = trim(($filtros['desde'] ?: 'Desde inicio') . ' — ' . ($filtros['hasta'] ?: 'Hasta hoy'));
            $lineas[] = 'Rango de fechas: ' . $rango;
        }

        if ($tipo === 'pagos' && !empty($filtros['metodo'])) {
            $lineas[] = 'Método de pago: ' . strtoupper($filtros['metodo']);
        }

        if ($tipo !== 'pagos' && !empty($filtros['estado'])) {
            $lineas[] = 'Estado filtrado: ' . ucfirst($filtros['estado']);
        }

        return $lineas;
    }

    private function resumenContextoPdf(): array
    {
        $usuario = Session::get('user') ?: [];
        $contexto = Session::get('context') ?: [];

        $colegio = 'Todos los colegios asignados';
        if (!empty($contexto['id_colegio'])) {
            foreach ($usuario['colegios_disponibles'] ?? [] as $item) {
                if ((int) $item['id_colegio'] === (int) $contexto['id_colegio']) {
                    $colegio = $item['nombre'];
                    break;
                }
            }
        } elseif (!empty($usuario['colegio_nombre'])) {
            $colegio = $usuario['colegio_nombre'];
        }

        $sede = 'Todas las sedes asignadas';
        if (!empty($contexto['id_sede'])) {
            foreach ($usuario['sedes_disponibles'] ?? [] as $item) {
                if ((int) $item['id_sede'] === (int) $contexto['id_sede']) {
                    $sede = $item['nombre'];
                    break;
                }
            }
        } elseif (!empty($usuario['sede_nombre'])) {
            $sede = $usuario['sede_nombre'];
        }

        return ['colegio' => $colegio, 'sede' => $sede];
    }

    private function descripcionUsuario(?array $usuario): string
    {
        if (!$usuario) {
            return '';
        }

        $roles = [
            'admin_global' => 'Administrador Global',
            'admin_colegio' => 'Administrador de Colegio',
            'agente' => 'Agente de Cobranzas',
        ];

        $rol = $roles[$usuario['rol'] ?? ''] ?? ucfirst((string) ($usuario['rol'] ?? ''));
        $nombre = $usuario['nombre_completo'] ?? ($usuario['usuario'] ?? '');

        if ($nombre === '') {
            return $rol;
        }

        return trim($nombre . ' — ' . $rol);
    }

    private function totalesReporte(string $tipo, array $datos): array
    {
        if (!$datos) {
            return [];
        }

        return match ($tipo) {
            'pagos' => $this->totalesPagos($datos),
            'acuerdos' => $this->totalesAcuerdos($datos),
            default => $this->totalesCartera($datos),
        };
    }

    private function totalesCartera(array $datos): array
    {
        $totalSaldo = 0.0;
        $vencidas = 0;
        foreach ($datos as $fila) {
            $totalSaldo += (float) ($fila['saldo_actual'] ?? 0);
            if (isset($fila['estado']) && strtolower((string) $fila['estado']) === 'vencida') {
                $vencidas++;
            }
        }

        return [
            'Saldo pendiente' => '$ ' . number_format($totalSaldo, 0, ',', '.'),
            'Deudas registradas' => number_format(count($datos), 0, ',', '.'),
            'Deudas vencidas' => number_format($vencidas, 0, ',', '.'),
        ];
    }

    private function totalesPagos(array $datos): array
    {
        $total = 0.0;
        foreach ($datos as $fila) {
            $total += (float) ($fila['valor_total'] ?? 0);
        }
        $promedio = $total > 0 && count($datos) > 0 ? $total / count($datos) : 0;

        return [
            'Recaudo total' => '$ ' . number_format($total, 0, ',', '.'),
            'Pagos registrados' => number_format(count($datos), 0, ',', '.'),
            'Promedio por pago' => '$ ' . number_format($promedio, 0, ',', '.'),
        ];
    }

    private function totalesAcuerdos(array $datos): array
    {
        $total = 0.0;
        $activos = 0;
        $cerrados = 0;
        foreach ($datos as $fila) {
            $total += (float) ($fila['monto_total'] ?? 0);
            $estado = strtolower((string) ($fila['estado'] ?? ''));
            if (in_array($estado, ['activo', 'en seguimiento', 'vigente'], true)) {
                $activos++;
            }
            if (in_array($estado, ['cerrado', 'finalizado'], true)) {
                $cerrados++;
            }
        }

        return [
            'Monto comprometido' => '$ ' . number_format($total, 0, ',', '.'),
            'Acuerdos activos' => number_format($activos, 0, ',', '.'),
            'Acuerdos cerrados' => number_format($cerrados, 0, ',', '.'),
        ];
    }
}
