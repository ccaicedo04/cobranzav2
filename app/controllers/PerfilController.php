<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class PerfilController extends Controller
{
    /** @var UsuarioModel */
    private $usuarios;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $this->usuarios = new UsuarioModel();
    }

    public function index()
    {
        $usuario = Session::get('user');
        $detalle = $this->usuarios->detalle((int) ($usuario['id_usuario'] ?? 0));
        if (!$detalle && isset($usuario['id_usuario'])) {
            $detalle = $this->usuarios->find((int) $usuario['id_usuario']);
        }
        $this->view('perfil/index', [
            'usuario' => $detalle,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=perfil');
        }

        $usuario = Session::get('user');
        $data = [
            'nombre_completo' => $_POST['nombre_completo'] ?? $usuario['nombre_completo'],
            'email' => $_POST['email'] ?? '',
        ];
        $this->usuarios->update($usuario['id_usuario'], $data);

        if (!empty($_POST['password'])) {
            $this->usuarios->updatePassword($usuario['id_usuario'], $_POST['password']);
        }

        $detalle = $this->usuarios->find($usuario['id_usuario']);
        Session::set('user', array_merge($usuario, [
            'nombre_completo' => $detalle['nombre_completo'],
        ]));

        Helpers::redirect('index.php?route=perfil');
    }
}
