<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\UsuarioModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class AuthController extends Controller
{
    private UsuarioModel $usuarios;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        $this->usuarios = new UsuarioModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';
            $csrf = $_POST['_token'] ?? '';

            if (!Helpers::validateCsrf($csrf)) {
                $this->view('auth/login', [
                    'error' => 'Token CSRF inválido.',
                    'token' => Helpers::csrfToken(),
                ]);
                return;
            }

            $user = $this->usuarios->authenticate($usuario, $password);

            if (!$user) {
                $this->view('auth/login', [
                    'error' => 'Credenciales inválidas.',
                    'token' => Helpers::csrfToken(),
                ]);
                return;
            }

            Session::set('user', [
                'id_usuario' => $user['id_usuario'],
                'nombre_completo' => $user['nombre_completo'],
                'rol' => $user['rol'],
                'id_colegio' => $user['id_colegio'],
                'id_sede' => $user['id_sede'],
            ]);
            Session::regenerate();

            $this->auditoria->create([
                'id_usuario' => $user['id_usuario'],
                'id_colegio' => $user['id_colegio'],
                'id_sede' => $user['id_sede'],
                'modulo' => 'autenticacion',
                'accion' => 'login',
                'detalle' => 'Inicio de sesión exitoso',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'fecha_registro' => date('Y-m-d H:i:s'),
            ]);

            Helpers::redirect('index.php');
            return;
        }

        $this->view('auth/login', [
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function logout(): void
    {
        $user = Session::get('user');
        if ($user) {
            $this->auditoria->create([
                'id_usuario' => $user['id_usuario'],
                'id_colegio' => $user['id_colegio'],
                'id_sede' => $user['id_sede'],
                'modulo' => 'autenticacion',
                'accion' => 'logout',
                'detalle' => 'Cierre de sesión',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'fecha_registro' => date('Y-m-d H:i:s'),
            ]);
        }

        Session::destroy();
        Helpers::redirect('index.php?route=auth/login');
    }
}
