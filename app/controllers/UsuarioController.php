<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\SedeModel;
use App\Models\UsuarioModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class UsuarioController extends Controller
{
    private UsuarioModel $usuarios;
    private SedeModel $sedes;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->usuarios = new UsuarioModel();
        $this->sedes = new SedeModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $lista = $this->usuarios->all();
        $this->view('administracion/usuarios/index', [
            'usuarios' => $lista,
            'sedes' => $this->sedes->all(),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuarioSesion = Session::get('user');
        $data = [
            'id_colegio' => $_POST['id_colegio'] ?? $usuarioSesion['id_colegio'],
            'id_sede' => $_POST['id_sede'] ?? $usuarioSesion['id_sede'],
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'email' => $_POST['email'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'password_hash' => password_hash($_POST['password'] ?? '123456', PASSWORD_DEFAULT),
            'rol' => $_POST['rol'] ?? 'agente',
            'estado' => $_POST['estado'] ?? 'activo',
        ];
        $id = $this->usuarios->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuarioSesion['id_usuario'],
            'id_colegio' => $usuarioSesion['id_colegio'],
            'id_sede' => $usuarioSesion['id_sede'],
            'modulo' => 'usuarios',
            'accion' => 'crear',
            'detalle' => 'Usuario ' . $id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=usuarios');
    }
}
