<?php

namespace App\Controllers;

use App\Models\ParametroModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ParametroController extends Controller
{
    private ParametroModel $parametros;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('parametrizacion');

        $this->parametros = new ParametroModel();
    }

    public function index(): void
    {
        $parametros = $this->parametros->all();
        $this->view('administracion/parametros/index', [
            'parametros' => $parametros,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=parametros');
        }

        $usuario = Session::get('user');
        $this->parametros->create([
            'clave' => $_POST['clave'] ?? '',
            'valor' => $_POST['valor'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'eliminado' => 0,
        ]);

        Helpers::redirect('index.php?route=parametros');
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=parametros');
        }

        $id = (int) ($_POST['id_parametro'] ?? 0);
        if (!$id) {
            Helpers::redirect('index.php?route=parametros');
        }

        $this->parametros->update($id, [
            'valor' => $_POST['valor'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
        ]);

        Helpers::redirect('index.php?route=parametros');
    }
}
