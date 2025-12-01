<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\CargaMasivaModel;
use App\Models\ComunicacionModel;
use App\Models\ConceptoModel;
use App\Models\DeudaModel;
use App\Models\EstudianteModel;
use App\Models\ReporteModel;
use App\Models\ResponsableModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;
use Core\SpreadsheetReader;

class CargaController extends Controller
{
    /** @var CargaMasivaModel */
    private $cargas;
    /** @var ReporteModel */
    private $reportes;
    /** @var ComunicacionModel */
    private $comunicaciones;
    /** @var ResponsableModel */
    private $responsables;
    /** @var EstudianteModel */
    private $estudiantes;
    /** @var ConceptoModel */
    private $conceptos;
    /** @var DeudaModel */
    private $deudas;
    /** @var AuditoriaModel */
    private $auditoria;

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
        $this->responsables = new ResponsableModel();
        $this->estudiantes = new EstudianteModel();
        $this->conceptos = new ConceptoModel();
        $this->deudas = new DeudaModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index()
    {
        $cargas = $this->cargas->all([], ['order' => 'fecha_registro DESC']);

        $this->view('carga_masiva/index', [
            'cargas' => $cargas,
            'ventana' => $this->resumenVentana($cargas),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        if (empty($tenant['id_colegio']) || empty($tenant['id_sede'])) {
            Session::set('flash_error', 'Selecciona colegio y sede antes de ejecutar la carga masiva.');
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $archivo = $_FILES['archivo'] ?? null;
        if (!$archivo || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Session::set('flash_error', 'Debes adjuntar el archivo de cartera (CSV o XLSX).');
            Helpers::redirect('index.php?route=carga-masiva');
        }

        $notas = trim((string) ($_POST['notas'] ?? ''));
        $nombreArchivo = $archivo['name'] ?? 'carga.xlsx';
        $rutaDestino = $this->moverArchivo($archivo);

        $idCarga = $this->cargas->create([
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'tipo_archivo' => pathinfo($nombreArchivo, PATHINFO_EXTENSION),
            'archivo_original' => $nombreArchivo,
            'archivo_procesado' => null,
            'total_registros' => 0,
            'total_errores' => 0,
            'resultado' => 'Pendiente',
            'mensaje' => $notas !== '' ? $notas : 'Carga masiva registrada',
            'usuario_registro' => $usuario['id_usuario'],
        ]);

        $backup = $this->generarBackup($tenant, $idCarga);
        $resultado = $this->procesarArchivo($rutaDestino, $tenant, $usuario, $idCarga, $backup);

        $this->cargas->update($idCarga, [
            'archivo_procesado' => basename($rutaDestino),
            'total_registros' => $resultado['registros'],
            'total_errores' => count($resultado['errores']),
            'resultado' => $resultado['estado'],
            'mensaje' => $resultado['mensaje'],
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

    private function procesarArchivo(string $ruta, array $tenant, array $usuario, int $idCarga, string $rutaBackup): array
    {
        $respuesta = [
            'registros' => 0,
            'errores' => [],
            'estado' => 'Pendiente',
            'mensaje' => 'Sin procesar',
        ];

        $rows = SpreadsheetReader::rows($ruta);
        if (!$rows) {
            $respuesta['estado'] = 'Falló';
            $respuesta['mensaje'] = 'No se pudieron leer filas del archivo. Verifica que sea CSV o XLSX válido.';
            return $respuesta;
        }

        $plan = $this->construirPlan($rows, $tenant);
        $errores = $plan['errores'];
        $creados = 0;
        $fecha = date('Y-m-d');

        foreach ($plan['estudiantes'] as $codigo => $bloque) {
            $responsable = $this->resolverResponsable($bloque['responsable'], $tenant);
            if (!$responsable) {
                $errores[] = 'No se pudo asociar responsable para el estudiante ' . $codigo;
                continue;
            }

            $estudiante = $this->resolverEstudiante($codigo, $bloque['nombre_alumno'], $responsable, $tenant);
            if (!$estudiante) {
                $errores[] = 'No se pudo crear o ubicar al estudiante ' . $codigo;
                continue;
            }

            $nuevasDeudas = [];
            $conceptos = $bloque['conceptos'];
            if (!$conceptos && $bloque['total'] > 0) {
                $conceptos[] = ['concepto' => 'Saldo cartera 2025', 'valor' => $bloque['total']];
            }

            foreach ($conceptos as $conceptoItem) {
                $valor = (float) ($conceptoItem['valor'] ?? 0);
                $nombreConcepto = trim((string) ($conceptoItem['concepto'] ?? ''));
                if ($valor <= 0 || $nombreConcepto === '') {
                    continue;
                }

                $concepto = $this->resolverConcepto($nombreConcepto, $tenant['id_colegio']);
                $nuevasDeudas[] = [
                    'id_concepto' => $concepto['id_concepto'],
                    'valor_inicial' => $valor,
                    'saldo_actual' => $valor,
                    'fecha_generacion' => $fecha,
                    'estado' => 'pendiente',
                    'notas' => 'Cargue masivo ' . date('Y-m') . ' (' . $nombreConcepto . ')',
                ];
            }

            if (!$nuevasDeudas) {
                continue;
            }

            $previo = $this->eliminarDeudasPrevias($estudiante['id_estudiante']);
            $sumaNueva = 0.0;
            foreach ($nuevasDeudas as $deuda) {
                $sumaNueva += (float) $deuda['saldo_actual'];
                $this->deudas->create([
                    'id_colegio' => $tenant['id_colegio'],
                    'id_sede' => $tenant['id_sede'],
                    'id_estudiante' => $estudiante['id_estudiante'],
                    'id_concepto' => $deuda['id_concepto'],
                    'id_periodo' => null,
                    'fecha_generacion' => $deuda['fecha_generacion'],
                    'valor_inicial' => $deuda['valor_inicial'],
                    'saldo_actual' => $deuda['saldo_actual'],
                    'estado' => $deuda['estado'],
                    'fecha_vencimiento' => null,
                    'notas' => $deuda['notas'],
                    'eliminado' => 0,
                ]);
            }

            $this->auditoria->create([
                'id_usuario' => $usuario['id_usuario'],
                'id_colegio' => $tenant['id_colegio'],
                'id_sede' => $tenant['id_sede'],
                'modulo' => 'carga_masiva',
                'accion' => 'actualizar',
                'detalle' => 'Estudiante ' . ($estudiante['nombre_completo'] ?? $codigo) . ': saldo previo $' . number_format($previo, 0, ',', '.') . ', nuevo $' . number_format($sumaNueva, 0, ',', '.') . ' (carga #' . $idCarga . ')',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'fecha_registro' => date('Y-m-d H:i:s'),
            ]);

            $creados++;
        }

        $estado = 'Exitoso';
        if ($errores && $creados > 0) {
            $estado = 'Parcial';
        } elseif ($errores && $creados === 0) {
            $estado = 'Falló';
        }

        $mensaje = 'Carga procesada: ' . $creados . ' estudiantes actualizados.';
        if ($errores) {
            $mensaje .= ' Errores: ' . implode(' | ', $errores);
        }
        if ($rutaBackup !== '') {
            $mensaje .= ' Backup: ' . basename($rutaBackup);
        }

        $respuesta['registros'] = $creados;
        $respuesta['errores'] = $errores;
        $respuesta['estado'] = $estado;
        $respuesta['mensaje'] = $mensaje;

        return $respuesta;
    }

    private function construirPlan(array $rows, array $tenant): array
    {
        $plan = [
            'estudiantes' => [],
            'errores' => [],
        ];

        $responsableActual = [];
        $codigoActual = null;

        foreach ($rows as $index => $row) {
            $documento = trim((string) ($row['documento_responsable'] ?? ''));
            $nombreResp = trim((string) ($row['nombre_responsable'] ?? ''));
            $correo = trim((string) ($row['correo'] ?? ''));
            $telefono = trim((string) ($row['telefono'] ?? ''));

            if ($documento !== '') {
                $responsableActual = [
                    'numero_documento' => $documento,
                    'nombre_completo' => $nombreResp,
                    'correo' => $correo,
                    'telefono' => $telefono,
                ];
            }

            $codigo = trim((string) ($row['codigo_alumno'] ?? ''));
            $concepto = trim((string) ($row['alumno_o_concepto'] ?? ''));
            $valor = $this->valorFila($row);

            if ($codigo !== '') {
                $codigoActual = $codigo;
                $plan['estudiantes'][$codigoActual] = [
                    'responsable' => $responsableActual,
                    'nombre_alumno' => $concepto !== '' ? $concepto : ('Alumno ' . $codigo),
                    'conceptos' => [],
                    'total' => $valor,
                ];
                continue;
            }

            if ($concepto === '' || $codigoActual === null) {
                continue;
            }

            $plan['estudiantes'][$codigoActual]['conceptos'][] = [
                'concepto' => $concepto,
                'valor' => $valor,
            ];
        }

        return $plan;
    }

    private function valorFila(array $row): float
    {
        $totalGeneral = $this->aNumero($row['total_general'] ?? 0);
        if ($totalGeneral > 0) {
            return $totalGeneral;
        }

        $campos = [
            'monto_julio',
            'monto_agosto',
            'monto_septiembre',
            'monto_octubre',
            'monto_formulario',
        ];

        $total = 0.0;
        foreach ($campos as $campo) {
            $total += $this->aNumero($row[$campo] ?? 0);
        }

        return $total;
    }

    private function aNumero($valor): float
    {
        if ($valor === null) {
            return 0.0;
        }

        $valor = trim((string) $valor);
        if ($valor === '') {
            return 0.0;
        }

        $valor = str_replace(['$', ' ', '.', ','], ['', '', '', ''], $valor);
        return (float) $valor;
    }

    private function eliminarDeudasPrevias(int $idEstudiante): float
    {
        $existentes = $this->deudas->all(['id_estudiante' => $idEstudiante, 'eliminado' => 0]);
        $total = 0.0;
        foreach ($existentes as $deuda) {
            $total += (float) ($deuda['saldo_actual'] ?? 0);
            $this->deudas->update((int) $deuda['id_deuda'], ['eliminado' => 1]);
        }

        return $total;
    }

    private function resolverResponsable(array $datos, array $tenant)
    {
        $responsable = null;
        if (!empty($datos['numero_documento'])) {
            $responsable = $this->responsables->buscarPorDocumento($datos['numero_documento'], $tenant['id_colegio'], $tenant['id_sede']);
        }

        $payload = [
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'nombre_completo' => $datos['nombre_completo'] ?? 'Responsable sin nombre',
            'tipo_documento' => 'CC',
            'numero_documento' => $datos['numero_documento'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'estado' => 'activo',
            'eliminado' => 0,
        ];

        if ($responsable) {
            $this->responsables->update((int) $responsable['id_responsable'], $payload);
            return $this->responsables->find((int) $responsable['id_responsable']);
        }

        $payload['nombre_completo'] = $payload['nombre_completo'] ?: 'Responsable ' . ($payload['numero_documento'] ?: uniqid('resp_'));
        $id = $this->responsables->create($payload);

        return $this->responsables->find($id);
    }

    private function resolverEstudiante(string $codigo, string $nombre, array $responsable, array $tenant)
    {
        $existente = $this->estudiantes->buscarPorCodigo($codigo, $tenant['id_colegio'], $tenant['id_sede']);
        $payload = [
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'id_responsable' => $responsable['id_responsable'],
            'codigo_estudiante' => $codigo,
            'nombre_completo' => $nombre ?: 'Estudiante ' . $codigo,
            'estado' => 'activo',
            'eliminado' => 0,
        ];

        if ($existente) {
            $this->estudiantes->update((int) $existente['id_estudiante'], $payload);
            return $this->estudiantes->find((int) $existente['id_estudiante']);
        }

        $id = $this->estudiantes->create($payload);
        return $this->estudiantes->find($id);
    }

    private function resolverConcepto(string $nombre, int $idColegio): array
    {
        $concepto = $this->conceptos->buscarPorNombre($nombre, $idColegio);
        if ($concepto) {
            return $concepto;
        }

        $id = $this->conceptos->create([
            'id_colegio' => $idColegio,
            'nombre' => $nombre,
            'descripcion' => 'Generado desde carga masiva',
            'tipo' => 'cartera',
            'valor_base' => 0,
            'estado' => 'activo',
            'eliminado' => 0,
        ]);

        return $this->conceptos->find($id);
    }

    private function moverArchivo(array $archivo): string
    {
        $destino = __DIR__ . '/../../uploads/cargas';
        if (!is_dir($destino)) {
            mkdir($destino, 0775, true);
        }

        $nombre = date('Ymd_His') . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $archivo['name'] ?? 'carga.xlsx');
        $ruta = $destino . '/' . $nombre;
        move_uploaded_file($archivo['tmp_name'], $ruta);

        return realpath($ruta) ?: $ruta;
    }

    private function generarBackup(array $tenant, int $idCarga): string
    {
        $destino = __DIR__ . '/../../uploads/backups';
        if (!is_dir($destino)) {
            mkdir($destino, 0775, true);
        }

        $snapshot = [
            'responsables' => $this->responsables->all(['id_colegio' => $tenant['id_colegio'], 'id_sede' => $tenant['id_sede']]),
            'estudiantes' => $this->estudiantes->all(['id_colegio' => $tenant['id_colegio'], 'id_sede' => $tenant['id_sede']]),
            'deudas' => $this->deudas->all(['id_colegio' => $tenant['id_colegio'], 'id_sede' => $tenant['id_sede']]),
            'generado_en' => date('Y-m-d H:i:s'),
            'carga' => $idCarga,
        ];

        $nombre = $destino . '/backup_carga_' . $idCarga . '_' . date('Ymd_His') . '.json';
        file_put_contents($nombre, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $nombre;
    }
}
