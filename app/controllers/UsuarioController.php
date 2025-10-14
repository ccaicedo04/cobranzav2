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
    private array $modulosPermitidos = ['cobranzas', 'administracion', 'parametrizacion'];

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('administracion');

        $this->usuarios = new UsuarioModel();
        $this->sedes = new SedeModel();
        $this->auditoria = new AuditoriaModel();
        $this->colegios = new ColegioModel();
    }

    public function index(): void
    {
        $listaBruto = $this->usuarios->listadoConContexto();
        $lista = array_map(function (array $fila): array {
            $fila['permisos_colegios_array'] = $this->decodeJson($fila['permisos_colegios'] ?? null);
            $fila['permisos_sedes_array'] = $this->decodeJson($fila['permisos_sedes'] ?? null);
            $fila['permisos_modulos_array'] = $this->decodeJson($fila['permisos_modulos'] ?? null);

            return $fila;
        }, $listaBruto);

        $usuario = Session::get('user');
        $contextoForm = $this->contextoSelecciones();

        $this->view('administracion/usuarios/index', [
            'usuarios' => $lista,
            'colegios' => $contextoForm['colegios'],
            'sedes' => $contextoForm['sedes'],
            'usuario' => $usuario,
            'modulos' => $this->modulosPermitidos,
            'mapColegios' => $contextoForm['mapColegios'],
            'mapSedes' => $contextoForm['mapSedes'],
            'token' => Helpers::csrfToken(),
        ]);
    }

    private function decodeJson(?string $payload): array
    {
        if (empty($payload)) {
            return [];
        }

        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuarioSesion = Session::get('user');
        $rol = $_POST['rol'] ?? 'agente';
        $permisosColegio = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_colegios'] ?? []))));
        $permisosSede = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_sedes'] ?? []))));
        $permisosModulo = array_values(array_unique(array_filter($_POST['permisos_modulos'] ?? [])));

        if ($rol === 'agente') {
            $permisosModulo = array_values(array_intersect($permisosModulo, ['cobranzas']));
            if (empty($permisosModulo)) {
                $permisosModulo = ['cobranzas'];
            }
        }

        if ($rol === 'agente' && empty($permisosSede)) {
            Helpers::redirect('index.php?route=usuarios');
        }

        if ($rol === 'admin_colegio' && empty($permisosColegio)) {
            Helpers::redirect('index.php?route=usuarios');
        }

        if ($rol === 'admin_global' && empty($permisosModulo)) {
            $permisosModulo = $this->modulosPermitidos;
        }

        $idColegio = null;
        $idSede = null;

        if ($permisosColegio) {
            $idColegio = $permisosColegio[0];
        } elseif (!empty($usuarioSesion['id_colegio'])) {
            $idColegio = $usuarioSesion['id_colegio'];
        }

        if ($permisosSede) {
            $idSede = $permisosSede[0];
        } elseif (!empty($usuarioSesion['id_sede'])) {
            $idSede = $usuarioSesion['id_sede'];
        }

        $data = [
            'id_colegio' => $idColegio ?: null,
            'id_sede' => $idSede ?: null,
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'email' => $_POST['email'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'password_hash' => password_hash($_POST['password'] ?? '123456', PASSWORD_DEFAULT),
            'rol' => $rol,
            'permisos_colegios' => $permisosColegio ? json_encode($permisosColegio, JSON_THROW_ON_ERROR) : null,
            'permisos_sedes' => $permisosSede ? json_encode($permisosSede, JSON_THROW_ON_ERROR) : null,
            'permisos_modulos' => $permisosModulo ? json_encode($permisosModulo, JSON_THROW_ON_ERROR) : null,
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

    public function detalle(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuario = $this->usuarios->detalle($id);
        if (!$usuario) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $colegiosIds = $this->decodeJson($usuario['permisos_colegios'] ?? null);
        $sedesIds = $this->decodeJson($usuario['permisos_sedes'] ?? null);
        $modulos = $this->decodeJson($usuario['permisos_modulos'] ?? null);

        $colegiosAsignados = $this->colegios->porIds($colegiosIds);
        $sedesAsignadas = $this->sedes->porIds($sedesIds);
        $contextoForm = $this->contextoSelecciones();

        $this->view('administracion/usuarios/detalle', [
            'usuarioDetalle' => $usuario,
            'colegios' => $colegiosAsignados,
            'sedes' => $sedesAsignadas,
            'modulos' => $modulos,
            'opcionesColegios' => $contextoForm['colegios'],
            'opcionesSedes' => $contextoForm['sedes'],
            'modulosDisponibles' => $this->modulosPermitidos,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $id = (int) ($_POST['id_usuario'] ?? 0);
        if ($id <= 0) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuarioSesion = Session::get('user');
        $rol = $_POST['rol'] ?? 'agente';
        $permisosColegio = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_colegios'] ?? []))));
        $permisosSede = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_sedes'] ?? []))));
        $permisosModulo = array_values(array_unique(array_filter($_POST['permisos_modulos'] ?? [])));

        if ($rol === 'agente') {
            $permisosModulo = array_values(array_intersect($permisosModulo, ['cobranzas']));
            if (empty($permisosModulo)) {
                $permisosModulo = ['cobranzas'];
            }
        }

        if ($rol === 'agente' && empty($permisosSede)) {
            Helpers::redirect('index.php?route=usuarios/detalle&id=' . $id);
        }

        if ($rol === 'admin_colegio' && empty($permisosColegio)) {
            Helpers::redirect('index.php?route=usuarios/detalle&id=' . $id);
        }

        if ($rol === 'admin_global' && empty($permisosModulo)) {
            $permisosModulo = $this->modulosPermitidos;
        }

        $idColegio = null;
        $idSede = null;

        if ($permisosColegio) {
            $idColegio = $permisosColegio[0];
        } elseif (!empty($usuarioSesion['id_colegio'])) {
            $idColegio = $usuarioSesion['id_colegio'];
        }

        if ($permisosSede) {
            $idSede = $permisosSede[0];
        } elseif (!empty($usuarioSesion['id_sede'])) {
            $idSede = $usuarioSesion['id_sede'];
        }

        $data = [
            'id_colegio' => $idColegio ?: null,
            'id_sede' => $idSede ?: null,
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'email' => $_POST['email'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'rol' => $rol,
            'permisos_colegios' => $permisosColegio ? json_encode($permisosColegio, JSON_THROW_ON_ERROR) : null,
            'permisos_sedes' => $permisosSede ? json_encode($permisosSede, JSON_THROW_ON_ERROR) : null,
            'permisos_modulos' => $permisosModulo ? json_encode($permisosModulo, JSON_THROW_ON_ERROR) : null,
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $password = trim((string) ($_POST['password'] ?? ''));
        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->usuarios->update($id, $data);

        $this->auditoria->create([
            'id_usuario' => $usuarioSesion['id_usuario'],
            'id_colegio' => $usuarioSesion['id_colegio'],
            'id_sede' => $usuarioSesion['id_sede'],
            'modulo' => 'usuarios',
            'accion' => 'actualizar',
            'detalle' => 'Actualización de usuario ' . $id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        if ((int) $usuarioSesion['id_usuario'] === $id) {
            $refrescado = $this->usuarios->detalle($id);
            if ($refrescado) {
                $refrescado['colegios_permitidos'] = $this->decodeJson($refrescado['permisos_colegios'] ?? null);
                $refrescado['sedes_permitidas'] = $this->decodeJson($refrescado['permisos_sedes'] ?? null);
                $refrescado['modulos_permitidos'] = $this->decodeJson($refrescado['permisos_modulos'] ?? null);
                Session::set('user', array_merge($usuarioSesion, $refrescado));
            }
        }

        Helpers::redirect('index.php?route=usuarios/detalle&id=' . $id);
    }

    private function contextoSelecciones(): array
    {
        $usuario = Session::get('user');
        $todosLosColegios = $this->colegios->all([], ['order' => 'nombre']);
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $todosLosColegios;
        } elseif (!empty($usuario['colegios_permitidos'])) {
            $colegios = $this->colegios->porIds($usuario['colegios_permitidos']);
        }

        $sedesConColegio = $this->sedes->conColegio();
        if ($usuario['rol'] !== 'admin_global' && !empty($usuario['sedes_permitidas'])) {
            $permitidas = array_map('intval', (array) $usuario['sedes_permitidas']);
            $sedesConColegio = array_values(array_filter(
                $sedesConColegio,
                static fn ($sede) => in_array((int) $sede['id_sede'], $permitidas, true)
            ));
        }

        $mapColegios = [];
        foreach ($todosLosColegios as $colegio) {
            $mapColegios[$colegio['id_colegio']] = $colegio['nombre'];
        }

        $mapSedes = [];
        foreach ($sedesConColegio as $sede) {
            $mapSedes[$sede['id_sede']] = $sede['nombre'];
        }

        return [
            'colegios' => $colegios,
            'sedes' => $sedesConColegio,
            'mapColegios' => $mapColegios,
            'mapSedes' => $mapSedes,
        ];
    }
}
