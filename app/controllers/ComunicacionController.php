<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\ComunicacionModel;
use App\Models\ConfiguracionModel;
use App\Models\DeudaModel;
use App\Models\EstudianteModel;
use App\Models\PlantillaModel;
use App\Models\ResponsableModel;
use App\Models\SedeModel;
use Core\Controller;
use Core\Helpers;
use Core\Mailer;
use Core\Session;
use RuntimeException;

class ComunicacionController extends Controller
{
    private ComunicacionModel $comunicaciones;
    private ResponsableModel $responsables;
    private EstudianteModel $estudiantes;
    private AuditoriaModel $auditoria;
    private PlantillaModel $plantillas;
    private ConfiguracionModel $configuracion;
    private DeudaModel $deudas;
    private SedeModel $sedes;
    private ColegioModel $colegios;

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
        $this->plantillas = new PlantillaModel();
        $this->configuracion = new ConfiguracionModel();
        $this->deudas = new DeudaModel();
        $this->sedes = new SedeModel();
        $this->colegios = new ColegioModel();
    }

    public function index(): void
    {
        $selectedResponsable = (int) ($_GET['responsable'] ?? 0);
        $selectedCanal = $_GET['canal'] ?? 'email';
        $selectedPlantilla = (int) ($_GET['plantilla'] ?? 0);

        $responsables = $this->responsables->conContexto();
        $estudiantes = $this->estudiantes->conContexto();
        $plantillas = $this->plantillas->all(['estado' => 'activo', 'eliminado' => 0], ['order' => 'canal, nombre']);
        $comunicaciones = $this->comunicaciones->all([], ['order' => 'fecha_envio DESC']);

        if (!$selectedResponsable && !empty($responsables)) {
            $selectedResponsable = (int) ($responsables[0]['id_responsable'] ?? 0);
        }

        $dataset = $this->armarDatasetComunicaciones($responsables, $estudiantes);

        $this->view('comunicaciones/index', [
            'comunicaciones' => $comunicaciones,
            'responsables' => $responsables,
            'estudiantes' => $estudiantes,
            'plantillas' => $plantillas,
            'dataset' => $dataset,
            'selectedResponsable' => $selectedResponsable,
            'selectedCanal' => $selectedCanal,
            'selectedPlantilla' => $selectedPlantilla,
            'status' => $_GET['status'] ?? null,
            'statusMessage' => $_GET['message'] ?? null,
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

        $idResponsable = (int) ($_POST['id_responsable'] ?? 0);
        if ($idResponsable <= 0) {
            Helpers::redirect('index.php?route=comunicaciones');
        }

        $detalleResponsable = $this->responsables->conContexto(['id_responsable' => $idResponsable]);
        $responsable = $detalleResponsable[0] ?? null;
        if (!$responsable) {
            Helpers::redirect('index.php?route=comunicaciones');
        }

        $estudiantesResponsable = $this->estudiantes->conContexto(['id_responsable' => $idResponsable]);
        $dataset = $this->armarDatasetComunicaciones($detalleResponsable, $estudiantesResponsable);
        $responsableDataset = $dataset['responsables'][$idResponsable] ?? null;
        if (!$responsableDataset) {
            Helpers::redirect('index.php?route=comunicaciones');
        }

        $idEstudiante = (int) ($_POST['id_estudiante'] ?? 0);
        $estudianteSeleccionado = null;
        foreach ($responsableDataset['estudiantes'] as $estudiante) {
            if ((int) $estudiante['id'] === $idEstudiante) {
                $estudianteSeleccionado = $estudiante;
                break;
            }
        }

        $canal = strtolower(trim((string) ($_POST['canal'] ?? 'email')));
        if (!in_array($canal, ['email', 'whatsapp', 'sms', 'llamada'], true)) {
            $canal = 'email';
        }

        $idPlantilla = (int) ($_POST['id_plantilla'] ?? 0);
        $plantilla = null;
        if ($idPlantilla > 0) {
            $plantilla = $this->plantillas->find($idPlantilla, ['estado' => 'activo', 'eliminado' => 0]);
        }

        $asunto = trim((string) ($_POST['asunto'] ?? ''));
        $mensaje = (string) ($_POST['mensaje'] ?? '');
        $resultado = trim((string) ($_POST['resultado'] ?? ''));

        $colegioInfo = $dataset['colegio'] ?? [];
        $sedeInfo = $dataset['sedes'][$responsable['id_sede']] ?? [];

        $placeholders = $this->construirPlaceholders($responsableDataset, $estudianteSeleccionado, $colegioInfo, $sedeInfo);
        $mensajeRenderizado = $this->renderTemplate($mensaje, $placeholders);
        if ($asunto === '' && $plantilla && !empty($plantilla['asunto_default'])) {
            $asunto = $this->renderTemplate((string) $plantilla['asunto_default'], $placeholders);
        }

        $idColegio = (int) ($tenant['id_colegio'] ?? $responsable['id_colegio'] ?? 0);
        $idSede = (int) ($tenant['id_sede'] ?? $responsable['id_sede'] ?? 0);

        $mensajeHtml = $mensajeRenderizado;
        $mensajePlano = strip_tags($mensajeRenderizado);
        if ($canal === 'email') {
            $mensajeHtml = $this->construirEmailProfesional($colegioInfo, $sedeInfo, $mensajeRenderizado);
            $mensajePlano = strip_tags($mensajeRenderizado);
        }

        $estadoEnvio = $canal === 'email' ? 'pendiente' : 'registrado';
        $detalleEnvio = $canal === 'email' ? 'Programado para envío inmediato' : 'Gestionado manualmente en canal ' . strtoupper($canal);

        if ($canal === 'email') {
            $configCorreo = $idColegio ? $this->configuracion->porColegio($idColegio) : null;
            if (!$configCorreo) {
                $estadoEnvio = 'error';
                $detalleEnvio = 'No se encontró configuración SMTP para el colegio seleccionado.';
            } else {
                try {
                    $smtpHost = trim((string) ($configCorreo['smtp_host'] ?? ''));
                    if ($smtpHost === '') {
                        $smtpHost = 'smtp.gmail.com';
                    } elseif (preg_match('/\.co$/i', $smtpHost)) {
                        $smtpHost = preg_replace('/\.co$/i', '.com', $smtpHost);
                    }

                    Mailer::send([
                        'host' => $smtpHost,
                        'port' => (int) ($configCorreo['smtp_puerto'] ?? 587),
                        'username' => $configCorreo['smtp_usuario'],
                        'password' => $configCorreo['smtp_password'],
                        'from_email' => $configCorreo['smtp_usuario'],
                        'from_name' => $colegioInfo['nombre'] ?? 'Gestión de cartera',
                    ], [
                        'to' => $responsable['correo'] ?? '',
                        'to_name' => $responsable['nombre_completo'] ?? '',
                        'subject' => $asunto,
                        'html' => $mensajeHtml,
                        'text' => $mensajePlano,
                    ]);
                    $estadoEnvio = 'enviado';
                    $detalleEnvio = 'Correo enviado correctamente el ' . date('Y-m-d H:i');
                    if ($resultado === '') {
                        $resultado = 'Correo enviado';
                    }
                } catch (RuntimeException $exception) {
                    $estadoEnvio = 'error';
                    $detalleEnvio = $exception->getMessage();
                    if ($resultado === '') {
                        $resultado = 'Error al enviar correo';
                    }
                }
            }
        } else {
            if ($resultado === '') {
                $resultado = 'Gestión registrada en canal ' . strtoupper($canal);
            }
        }

        $data = [
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
            'id_responsable' => $idResponsable,
            'id_estudiante' => $idEstudiante ?: null,
            'id_plantilla' => $idPlantilla ?: null,
            'tipo' => $_POST['tipo'] ?? 'gestion',
            'canal' => $canal,
            'asunto' => $asunto,
            'mensaje' => $mensajeHtml,
            'resultado' => $resultado,
            'fecha_envio' => date('Y-m-d H:i:s'),
            'usuario_registro' => $usuario['id_usuario'],
            'estado_envio' => $estadoEnvio,
            'detalle_envio' => $detalleEnvio,
            'eliminado' => 0,
        ];

        $this->comunicaciones->create($data);

        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $idColegio ?: null,
            'id_sede' => $idSede ?: null,
            'modulo' => 'comunicaciones',
            'accion' => 'registrar',
            'detalle' => sprintf(
                'Gestión %s (%s) para %s',
                strtoupper($canal),
                $estadoEnvio,
                $responsable['nombre_completo'] ?? ('ID ' . $idResponsable)
            ),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        $status = $estadoEnvio === 'error' ? 'error' : 'enviado';
        $message = $estadoEnvio === 'error' ? $detalleEnvio : 'Gestión registrada correctamente.';
        $redirect = 'index.php?route=comunicaciones&status=' . rawurlencode($status)
            . '&message=' . rawurlencode($message)
            . '&responsable=' . $idResponsable
            . '&canal=' . rawurlencode($canal);

        Helpers::redirect($redirect);
    }

    private function armarDatasetComunicaciones(array $responsables, array $estudiantes): array
    {
        $mapEstudiantes = [];
        foreach ($estudiantes as $estudiante) {
            $responsableId = (int) ($estudiante['id_responsable'] ?? 0);
            if ($responsableId === 0) {
                continue;
            }

            if (!isset($mapEstudiantes[$responsableId])) {
                $mapEstudiantes[$responsableId] = [];
            }

            $mapEstudiantes[$responsableId][$estudiante['id_estudiante']] = [
                'id' => (int) $estudiante['id_estudiante'],
                'nombre' => $estudiante['nombre_completo'] ?? '',
                'codigo' => $estudiante['codigo_estudiante'] ?? '',
                'grado' => $estudiante['grado'] ?? '',
                'curso' => $estudiante['curso'] ?? '',
                'estado' => $estudiante['estado'] ?? '',
                'saldo' => 0.0,
                'saldo_formateado' => '$ 0',
                'fecha_vencimiento' => '',
                'conceptos' => [],
            ];
        }

        $responsablesData = [];
        $sedeIds = [];
        $colegioId = null;

        foreach ($responsables as $responsable) {
            $idResponsable = (int) ($responsable['id_responsable'] ?? 0);
            if ($idResponsable === 0) {
                continue;
            }

            $sedeId = (int) ($responsable['id_sede'] ?? 0);
            if ($sedeId) {
                $sedeIds[] = $sedeId;
            }
            if ($colegioId === null && isset($responsable['id_colegio'])) {
                $colegioId = (int) $responsable['id_colegio'];
            }

            $estudiantesResponsable = $mapEstudiantes[$idResponsable] ?? [];
            $deudas = $this->deudas->porResponsable($idResponsable);

            $saldoTotal = 0.0;
            $deudasActivas = 0;
            $proximoVencimiento = null;

            foreach ($deudas as $deuda) {
                $idEstudiante = (int) ($deuda['id_estudiante'] ?? 0);
                if (!isset($estudiantesResponsable[$idEstudiante])) {
                    $estudiantesResponsable[$idEstudiante] = [
                        'id' => $idEstudiante,
                        'nombre' => $deuda['estudiante_nombre'] ?? '',
                        'codigo' => '',
                        'grado' => '',
                        'curso' => '',
                        'estado' => '',
                        'saldo' => 0.0,
                        'saldo_formateado' => '$ 0',
                        'fecha_vencimiento' => '',
                        'conceptos' => [],
                    ];
                }

                $saldo = (float) ($deuda['saldo_actual'] ?? 0);
                $estudiantesResponsable[$idEstudiante]['saldo'] += $saldo;
                if (!empty($deuda['fecha_vencimiento'])) {
                    $fecha = (string) $deuda['fecha_vencimiento'];
                    if ($estudiantesResponsable[$idEstudiante]['fecha_vencimiento'] === '' || $fecha < $estudiantesResponsable[$idEstudiante]['fecha_vencimiento']) {
                        $estudiantesResponsable[$idEstudiante]['fecha_vencimiento'] = $fecha;
                    }
                    if ($proximoVencimiento === null || $fecha < $proximoVencimiento) {
                        $proximoVencimiento = $fecha;
                    }
                }

                if (!empty($deuda['concepto_nombre'])) {
                    $concepto = (string) $deuda['concepto_nombre'];
                    if (!in_array($concepto, $estudiantesResponsable[$idEstudiante]['conceptos'], true)) {
                        $estudiantesResponsable[$idEstudiante]['conceptos'][] = $concepto;
                    }
                }

                $saldoTotal += $saldo;
                if (strtolower((string) ($deuda['estado'] ?? '')) !== 'pagado') {
                    $deudasActivas++;
                }
            }

            foreach ($estudiantesResponsable as &$estudiante) {
                $estudiante['saldo_formateado'] = '$ ' . number_format($estudiante['saldo'], 0, ',', '.');
                $estudiante['conceptos'] = implode(', ', $estudiante['conceptos']);
            }
            unset($estudiante);

            $responsablesData[$idResponsable] = [
                'id' => $idResponsable,
                'nombre' => $responsable['nombre_completo'] ?? '',
                'correo' => $responsable['correo'] ?? '',
                'telefono' => $responsable['telefono'] ?? '',
                'documento' => trim(($responsable['tipo_documento'] ?? '') . ' ' . ($responsable['numero_documento'] ?? '')),
                'estado' => $responsable['estado'] ?? '',
                'sede_id' => $sedeId,
                'colegio_id' => (int) ($responsable['id_colegio'] ?? 0),
                'sede_nombre' => $responsable['sede_nombre'] ?? '',
                'colegio_nombre' => $responsable['colegio_nombre'] ?? '',
                'totales' => [
                    'saldo' => $saldoTotal,
                    'saldo_formateado' => '$ ' . number_format($saldoTotal, 0, ',', '.'),
                    'deudas_activas' => $deudasActivas,
                    'proximo_vencimiento' => $proximoVencimiento,
                ],
                'estudiantes' => array_values($estudiantesResponsable),
            ];
        }

        $sedesMap = [];
        $sedeIds = array_values(array_unique(array_filter($sedeIds)));
        if ($sedeIds) {
            foreach ($this->sedes->porIds($sedeIds) as $sede) {
                $sedesMap[(int) $sede['id_sede']] = [
                    'id' => (int) $sede['id_sede'],
                    'nombre' => $sede['nombre'] ?? '',
                    'telefono' => $sede['telefono'] ?? '',
                    'correo' => $sede['correo'] ?? '',
                    'direccion' => $sede['direccion'] ?? '',
                    'colegio_nombre' => $sede['colegio_nombre'] ?? '',
                ];
            }
        }

        $colegio = $colegioId ? $this->colegios->find($colegioId) : null;
        $colegioInfo = [
            'id' => $colegio['id_colegio'] ?? null,
            'nombre' => $colegio['nombre'] ?? '',
            'telefono' => $colegio['telefono'] ?? '',
            'correo' => $colegio['correo'] ?? '',
            'direccion' => $colegio['direccion'] ?? '',
            'logo' => $colegio['logo'] ?? '',
        ];
        $colegioInfo['logo_url'] = !empty($colegioInfo['logo']) ? Helpers::baseUrl($colegioInfo['logo']) : '';

        return [
            'responsables' => $responsablesData,
            'sedes' => $sedesMap,
            'colegio' => $colegioInfo,
        ];
    }

    private function construirPlaceholders(array $responsable, ?array $estudiante, array $colegio, array $sede): array
    {
        $saldoTotal = $responsable['totales']['saldo'] ?? 0;
        $proximoVencimiento = $responsable['totales']['proximo_vencimiento'] ?? '';

        if (!$estudiante && !empty($responsable['estudiantes'])) {
            $estudiante = $responsable['estudiantes'][0];
        }

        $saldoEstudiante = $estudiante['saldo'] ?? 0;
        $fechaVencimiento = $estudiante['fecha_vencimiento'] ?? '';
        if ($fechaVencimiento === '' && $proximoVencimiento) {
            $fechaVencimiento = $proximoVencimiento;
        }

        $telefonoContacto = $sede['telefono'] ?? ($colegio['telefono'] ?? '');
        $correoContacto = $sede['correo'] ?? ($colegio['correo'] ?? '');

        return [
            'responsable_nombre' => $responsable['nombre'] ?? '',
            'responsable_documento' => $responsable['documento'] ?? '',
            'responsable_correo' => $responsable['correo'] ?? '',
            'responsable_telefono' => $responsable['telefono'] ?? '',
            'estudiante_nombre' => $estudiante['nombre'] ?? '',
            'codigo_estudiante' => $estudiante['codigo'] ?? '',
            'grado' => $estudiante['grado'] ?? '',
            'curso' => $estudiante['curso'] ?? '',
            'concepto' => $estudiante['conceptos'] ?? '',
            'saldo_pendiente' => '$ ' . number_format($saldoEstudiante ?: $saldoTotal, 0, ',', '.'),
            'saldo_total' => '$ ' . number_format($saldoTotal, 0, ',', '.'),
            'fecha_vencimiento' => $fechaVencimiento ?? '',
            'colegio_nombre' => $colegio['nombre'] ?? '',
            'sede_nombre' => $sede['nombre'] ?? ($responsable['sede_nombre'] ?? ''),
            'telefono_contacto' => $telefonoContacto,
            'correo_contacto' => $correoContacto,
            'direccion_colegio' => $colegio['direccion'] ?? '',
        ];
    }

    private function renderTemplate(string $contenido, array $variables): string
    {
        if ($contenido === '') {
            return '';
        }

        $map = [];
        foreach ($variables as $clave => $valor) {
            $map[strtolower($clave)] = (string) $valor;
        }

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', static function (array $coincidencia) use ($map): string {
            $clave = strtolower($coincidencia[1]);

            return $map[$clave] ?? '';
        }, $contenido) ?? $contenido;
    }

    private function construirEmailProfesional(array $colegio, array $sede, string $contenido): string
    {
        $logo = $colegio['logo_url'] ?? '';
        $colegioNombre = $colegio['nombre'] ?? 'Colegio';
        $sedeNombre = $sede['nombre'] ?? '';
        $telefono = $sede['telefono'] ?? ($colegio['telefono'] ?? '');
        $correo = $sede['correo'] ?? ($colegio['correo'] ?? '');
        $direccion = $sede['direccion'] ?? ($colegio['direccion'] ?? '');

        $encabezado = '<div style="background:#102f5a;color:#ffffff;padding:24px 32px;border-radius:18px 18px 0 0;">'
            . ($logo ? '<img src="' . htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($colegioNombre, ENT_QUOTES, 'UTF-8') . '" style="height:48px;margin-bottom:12px;display:block;">' : '')
            . '<div style="font-size:20px;font-weight:700;letter-spacing:.3px;">' . htmlspecialchars($colegioNombre, ENT_QUOTES, 'UTF-8') . '</div>'
            . ($sedeNombre ? '<div style="font-size:14px;opacity:.85;">Sede ' . htmlspecialchars($sedeNombre, ENT_QUOTES, 'UTF-8') . '</div>' : '')
            . '</div>';

        $cuerpo = '<div style="padding:32px;font-size:15px;line-height:1.6;color:#1f2937;">' . $contenido . '</div>';

        $pie = '<div style="background:#f1f5f9;padding:24px 32px;border-radius:0 0 18px 18px;font-size:13px;color:#475569;">'
            . '<div style="font-weight:600;margin-bottom:6px;">Área de cartera — ' . htmlspecialchars($colegioNombre, ENT_QUOTES, 'UTF-8') . '</div>'
            . ($direccion ? '<div>Dirección: ' . htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') . '</div>' : '')
            . ($telefono ? '<div>Teléfono: ' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '</div>' : '')
            . ($correo ? '<div>Correo: ' . htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') . '</div>' : '')
            . '<div style="margin-top:10px;font-size:12px;opacity:.7;">Mensaje generado desde la plataforma de cobranzas.</div>'
            . '</div>';

        return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>'
            . htmlspecialchars($colegioNombre, ENT_QUOTES, 'UTF-8') . '</title></head><body style="margin:0;background:#e2e8f0;font-family:Inter,Arial,sans-serif;">'
            . '<div style="max-width:720px;margin:32px auto;background:#ffffff;border-radius:18px;box-shadow:0 24px 60px rgba(15,47,90,.18);overflow:hidden;">'
            . $encabezado . $cuerpo . $pie
            . '</div></body></html>';
    }
}
