<?php

namespace App\Controllers;

use App\Services\ResetCobranzasService;
use Core\Controller;
use Core\Helpers;
use Core\Session;
use Throwable;

class MantenimientoController extends Controller
{
    /** @var ResetCobranzasService */
    private $reset;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('administracion');
        $this->reset = new ResetCobranzasService();
    }

    public function limpiarCobranzas()
    {
        $this->protegerAdmin();

        $this->view('administracion/mantenimiento/limpieza_cobranzas', [
            'resumen' => $this->reset->resumen(),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function ejecutarLimpieza()
    {
        $this->protegerAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=mantenimiento/limpiar-cobranzas');
        }

        try {
            $path = $this->reset->respaldarYLimpiar();
            $nombre = basename($path);

            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $nombre . '"');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        } catch (Throwable $throwable) {
            http_response_code(500);
            echo 'No se pudo completar la limpieza: ' . $throwable->getMessage();
            exit;
        }
    }

    private function protegerAdmin(): void
    {
        $user = Session::get('user');
        if (!$user || ($user['rol'] ?? '') !== 'admin_global') {
            Helpers::redirect('index.php');
        }
    }
}
