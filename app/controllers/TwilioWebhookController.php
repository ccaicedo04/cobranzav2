<?php

namespace App\Controllers;

use App\Models\ComunicacionAdjuntoModel;
use App\Models\ComunicacionModel;
use App\Models\ConfiguracionModel;
use App\Models\ResponsableModel;
use App\Services\TwilioService;
use Core\Controller;

class TwilioWebhookController extends Controller
{
    /** @var ComunicacionModel */
    private $comunicaciones;
    /** @var ResponsableModel */
    private $responsables;
    /** @var ComunicacionAdjuntoModel */
    private $adjuntos;
    /** @var ConfiguracionModel */
    private $configuracion;

    public function __construct()
    {
        parent::__construct();
        $this->comunicaciones = new ComunicacionModel();
        $this->responsables = new ResponsableModel();
        $this->adjuntos = new ComunicacionAdjuntoModel();
        $this->configuracion = new ConfiguracionModel();
    }

    public function incoming()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->respondXml();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $from = (string) ($_POST['From'] ?? '');
        $body = trim((string) ($_POST['Body'] ?? ''));
        $sid = (string) ($_POST['MessageSid'] ?? ($_POST['SmsSid'] ?? ''));
        $numMedia = (int) ($_POST['NumMedia'] ?? 0);
        $timestamp = (string) ($_POST['Timestamp'] ?? ($_POST['DateCreated'] ?? ''));
        $canal = str_contains(strtolower($from), 'whatsapp:') ? 'whatsapp' : 'sms';

        $telefono = $this->sanitizePhone($from);
        if ($telefono === '') {
            $this->respondXml();
            return;
        }

        $responsable = $this->responsables->buscarPorTelefono($telefono);
        if (!$responsable) {
            $this->respondXml();
            return;
        }

        $fechaEnvio = $this->parseTimestamp($timestamp) ?: date('Y-m-d H:i:s');

        $mensaje = $body !== '' ? $body : '[Sin texto]';
        $detalle = $sid ? 'Mensaje recibido vía Twilio SID ' . $sid : 'Mensaje recibido vía Twilio';

        $id = $this->comunicaciones->create([
            'id_colegio' => (int) ($responsable['id_colegio'] ?? 0),
            'id_sede' => (int) ($responsable['id_sede'] ?? 0),
            'id_responsable' => (int) ($responsable['id_responsable'] ?? 0),
            'id_estudiante' => null,
            'id_plantilla' => null,
            'tipo' => 'inbound',
            'canal' => $canal,
            'asunto' => ucfirst($canal) . ' entrante',
            'mensaje' => $mensaje,
            'resultado' => 'Mensaje recibido desde Twilio',
            'fecha_envio' => $fechaEnvio,
            'usuario_registro' => 0,
            'estado_envio' => 'registrado',
            'detalle_envio' => $detalle,
            'eliminado' => 0,
        ]);

        $twilio = $this->crearServicioTwilio((int) ($responsable['id_colegio'] ?? 0));

        if ($numMedia > 0 && $twilio->ready()) {
            $this->guardarAdjuntos($id, $numMedia, $twilio);
        }

        $this->respondXml();
    }

    private function guardarAdjuntos(int $idComunicacion, int $numMedia, TwilioService $twilio)
    {
        $basePath = dirname(__DIR__, 2) . '/uploads/comunicaciones/' . $idComunicacion;
        if (!is_dir($basePath)) {
            mkdir($basePath, 0775, true);
        }

        for ($i = 0; $i < $numMedia; $i++) {
            $mediaUrl = (string) ($_POST['MediaUrl' . $i] ?? '');
            if ($mediaUrl === '') {
                continue;
            }

            $contentType = (string) ($_POST['MediaContentType' . $i] ?? 'application/octet-stream');
            $extension = $this->extensionDesdeTipo($contentType, $mediaUrl);
            $filename = 'twilio-' . $idComunicacion . '-' . ($i + 1) . $extension;
            $relative = 'uploads/comunicaciones/' . $idComunicacion . '/' . $filename;
            $absolute = $basePath . '/' . $filename;

            try {
                $descarga = $twilio->downloadMedia($mediaUrl);
                file_put_contents($absolute, $descarga['content']);
                $this->adjuntos->create([
                    'id_comunicacion' => $idComunicacion,
                    'nombre' => $filename,
                    'ruta' => $relative,
                    'tipo' => $descarga['content_type'] ?? $contentType,
                    'tamano' => $descarga['size'] ?? null,
                    'metadata' => json_encode(['media_url' => $mediaUrl], JSON_UNESCAPED_UNICODE),
                ]);
            } catch (\Throwable $exception) {
                // No interrumpimos el proceso si falla la descarga de un adjunto.
                continue;
            }
        }
    }

    private function sanitizePhone(string $phone): string
    {
        $phone = trim($phone);
        $phone = str_replace(['whatsapp:', '+'], '', strtolower($phone));
        $phone = preg_replace('/\D+/', '', $phone);

        return $phone ?: '';
    }

    /**
     * @return string|null
     */
    private function parseTimestamp(string $timestamp)
    {
        if ($timestamp === '') {
            return null;
        }

        $time = strtotime($timestamp);
        if ($time === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $time);
    }

    private function extensionDesdeTipo(string $tipo, string $url): string
    {
        $map = [
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
            'application/pdf' => '.pdf',
            'audio/mpeg' => '.mp3',
            'video/mp4' => '.mp4',
        ];

        if (isset($map[$tipo])) {
            return $map[$tipo];
        }

        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ($ext) {
                return '.' . strtolower($ext);
            }
        }

        return '.bin';
    }

    private function respondXml()
    {
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
    }

    private function crearServicioTwilio(int $idColegio): TwilioService
    {
        $config = $idColegio > 0 ? $this->configuracion->obtenerTwilio($idColegio) : null;

        return new TwilioService(
            $config['account_sid'] ?? null,
            $config['auth_token'] ?? null,
            $config['whatsapp_from'] ?? null,
            $config['sms_from'] ?? null,
            $config['default_country'] ?? null,
            $config['status_callback'] ?? null
        );
    }
}
