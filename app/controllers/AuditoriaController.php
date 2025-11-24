<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class AuditoriaController extends Controller
{
    /** @var AuditoriaModel */
    private $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('administracion');

        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $registros = $this->auditoria->conUsuarios();
        $this->view('administracion/auditoria/index', [
            'registros' => $registros,
        ]);
    }
}
