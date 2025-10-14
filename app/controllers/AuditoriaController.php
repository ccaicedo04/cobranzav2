<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class AuditoriaController extends Controller
{
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $registros = $this->auditoria->all([], ['order' => 'fecha_registro DESC']);
        $this->view('administracion/auditoria/index', [
            'registros' => $registros,
        ]);
    }
}
