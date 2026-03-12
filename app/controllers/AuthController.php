<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\SedeModel;
use App\Models\UsuarioModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class AuthController extends Controller
{
    private UsuarioModel $usuarios;
    private AuditoriaModel $auditoria;
    private SedeModel $sedes;
    private ColegioModel $colegios;

    public function __construct()
    {
        parent::__construct();
        $this->usuarios = new UsuarioModel();
        $this->auditoria = new AuditoriaModel();
        $this->sedes = new SedeModel();
        $this->colegios = new ColegioModel();
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

            $asignaciones = $user['asignaciones'] ?? ['colegios' => [], 'sedes' => [], 'modulos' => []];
            $colegiosPermitidos = array_map(
                static fn (array $colegio) => (int) $colegio['id_colegio'],
                $asignaciones['colegios']
            );
            $sedesPermitidas = array_map(
                static fn (array $sede) => (int) $sede['id_sede'],
                $asignaciones['sedes']
            );
            $modulosPermitidos = array_map(
                static fn (array $modulo) => $modulo['codigo'],
                $asignaciones['modulos']
            );

            if (!$modulosPermitidos) {
                $modulosPermitidos = match ($user['rol']) {
                    'admin_global', 'admin_colegio' => ['cobranzas', 'administracion', 'parametrizacion'],
                    default => ['cobranzas'],
                };
            }

            if (!$colegiosPermitidos && !empty($user['id_colegio'])) {
                $colegiosPermitidos = [(int) $user['id_colegio']];
            }

            if (!$sedesPermitidas && !empty($user['id_sede'])) {
                $sedesPermitidas = [(int) $user['id_sede']];
            }

            $colegiosAsignados = $asignaciones['colegios'];
            if (!$colegiosAsignados && $colegiosPermitidos) {
                $colegiosAsignados = $this->colegios->porIds($colegiosPermitidos);
            }

            $sedesAsignadas = $asignaciones['sedes'];
            if (!$sedesAsignadas && $sedesPermitidas) {
                $sedesAsignadas = $this->sedes->porIds($sedesPermitidas);
            }

            $colegioNombre = $user['colegio_nombre'] ?? null;
            if (!$colegioNombre && $colegiosAsignados) {
                $colegioNombre = count($colegiosAsignados) === 1
                    ? $colegiosAsignados[0]['nombre']
                    : 'Colegios asignados';
            }

            $sedeNombre = $user['sede_nombre'] ?? null;
            if (!$sedeNombre && $sedesAsignadas) {
                $sedeNombre = count($sedesAsignadas) === 1
                    ? ($sedesAsignadas[0]['nombre'] ?? 'Sede asignada')
                    : 'Sedes asignadas';
            }

            Session::set('user', [
                'id_usuario' => $user['id_usuario'],
                'nombre_completo' => $user['nombre_completo'],
                'rol' => $user['rol'],
                'id_colegio' => $colegiosPermitidos[0] ?? $user['id_colegio'],
                'id_sede' => $sedesPermitidas[0] ?? $user['id_sede'],
                'colegio_nombre' => $colegioNombre,
                'sede_nombre' => $sedeNombre,
                'colegios_permitidos' => $colegiosPermitidos,
                'sedes_permitidas' => $sedesPermitidas,
                'modulos_permitidos' => $modulosPermitidos,
                'colegios_disponibles' => array_map(
                    static fn (array $colegio): array => [
                        'id_colegio' => (int) $colegio['id_colegio'],
                        'nombre' => $colegio['nombre'],
                    ],
                    $colegiosAsignados
                ),
                'sedes_disponibles' => array_map(
                    static fn (array $sede): array => [
                        'id_sede' => (int) $sede['id_sede'],
                        'nombre' => $sede['nombre'],
                        'id_colegio' => (int) $sede['id_colegio'],
                    ],
                    $sedesAsignadas
                ),
            ]);
            Session::set('context', [
                'id_colegio' => $colegiosPermitidos[0] ?? null,
                'id_sede' => $sedesPermitidas[0] ?? null,
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
