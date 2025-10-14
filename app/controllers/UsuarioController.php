<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
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
    private ColegioModel $colegios;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->usuarios = new UsuarioModel();
        $this->sedes = new SedeModel();
        $this->auditoria = new AuditoriaModel();
        $this->colegios = new ColegioModel();
    }

    public function index(): void
    {
        $lista = $this->usuarios->listadoConContexto();
        $usuario = Session::get('user');
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        } elseif (!empty($usuario['id_colegio'])) {
            $colegios = $this->colegios->all(['id_colegio' => $usuario['id_colegio']]);
        }

        $this->view('administracion/usuarios/index', [
            'usuarios' => $lista,
            'colegios' => $colegios,
            'sedes' => $this->sedes->conColegio(),
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuarioSesion = Session::get('user');
        $rol = $_POST['rol'] ?? 'agente';
        $idColegio = $_POST['id_colegio'] ?? $usuarioSesion['id_colegio'] ?? null;
        $idSede = $_POST['id_sede'] ?? $usuarioSesion['id_sede'] ?? null;

        if ($rol === 'admin_global') {
            $idColegio = null;
            $idSede = null;
        }

        if ($rol !== 'admin_global' && empty($idColegio)) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $data = [
            'id_colegio' => $idColegio ?: null,
            'id_sede' => $idSede ?: null,
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'email' => $_POST['email'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'password_hash' => password_hash($_POST['password'] ?? '123456', PASSWORD_DEFAULT),
            'rol' => $rol,
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
