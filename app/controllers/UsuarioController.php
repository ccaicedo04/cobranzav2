<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\ModuloModel;
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
    private ModuloModel $modulos;

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
        $this->modulos = new ModuloModel();
    }

    public function index(): void
    {
        $lista = $this->usuarios->listadoConContexto();

        $usuario = Session::get('user');
        $contextoForm = $this->contextoSelecciones();
        $modulosDisponibles = $this->modulos->activos();
        $mapModulos = [];
        foreach ($modulosDisponibles as $modulo) {
            $mapModulos[$modulo['codigo']] = $modulo['nombre'];
        }

        $this->view('administracion/usuarios/index', [
            'usuarios' => $lista,
            'colegios' => $contextoForm['colegios'],
            'sedes' => $contextoForm['sedes'],
            'usuario' => $usuario,
            'modulos' => $modulosDisponibles,
            'mapColegios' => $contextoForm['mapColegios'],
            'mapSedes' => $contextoForm['mapSedes'],
            'mapModulos' => $mapModulos,
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
            $permisosModulo = array_column($this->modulos->activos(), 'codigo');
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
            'estado' => $_POST['estado'] ?? 'activo',
        ];
        $id = $this->usuarios->create($data);
        $this->usuarios->syncColegios($id, $permisosColegio);
        $this->usuarios->syncSedes($id, $permisosSede);
        $this->usuarios->syncModulosPorCodigo($id, $permisosModulo);
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

        $asignaciones = $usuario['asignaciones'];
        $colegiosIds = array_map(static fn ($colegio) => (int) $colegio['id_colegio'], $asignaciones['colegios']);
        $sedesIds = array_map(static fn ($sede) => (int) $sede['id_sede'], $asignaciones['sedes']);
        $modulos = array_map(static fn ($modulo) => $modulo['codigo'], $asignaciones['modulos']);

        $colegiosAsignados = $asignaciones['colegios'];
        $sedesAsignadas = $asignaciones['sedes'];
        $modulosDisponibles = $this->modulos->activos();
        $contextoForm = $this->contextoSelecciones();

        $this->view('administracion/usuarios/detalle', [
            'usuarioDetalle' => $usuario,
            'colegios' => $colegiosAsignados,
            'sedes' => $sedesAsignadas,
            'modulos' => $modulos,
            'opcionesColegios' => $contextoForm['colegios'],
            'opcionesSedes' => $contextoForm['sedes'],
            'modulosDisponibles' => $modulosDisponibles,
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
            $permisosModulo = array_column($this->modulos->activos(), 'codigo');
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
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $password = trim((string) ($_POST['password'] ?? ''));
        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->usuarios->update($id, $data);
        $this->usuarios->syncColegios($id, $permisosColegio);
        $this->usuarios->syncSedes($id, $permisosSede);
        $this->usuarios->syncModulosPorCodigo($id, $permisosModulo);

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
                $asignacionesActualizadas = $this->usuarios->asignacionesUsuario($id);
                $primerColegio = $asignacionesActualizadas['colegios'][0] ?? null;
                $primerSede = $asignacionesActualizadas['sedes'][0] ?? null;
                Session::set('user', array_merge($usuarioSesion, [
                    'nombre_completo' => $refrescado['nombre_completo'],
                    'email' => $refrescado['email'],
                    'usuario' => $refrescado['usuario'],
                    'rol' => $refrescado['rol'],
                    'id_colegio' => $refrescado['id_colegio'],
                    'id_sede' => $refrescado['id_sede'],
                    'colegio_nombre' => $primerColegio['nombre'] ?? ($refrescado['colegio_nombre'] ?? ($usuarioSesion['colegio_nombre'] ?? '')),
                    'sede_nombre' => $primerSede['nombre'] ?? ($refrescado['sede_nombre'] ?? ($usuarioSesion['sede_nombre'] ?? '')),
                    'colegios_permitidos' => array_map(
                        static fn ($colegio) => (int) $colegio['id_colegio'],
                        $asignacionesActualizadas['colegios']
                    ),
                    'sedes_permitidas' => array_map(
                        static fn ($sede) => (int) $sede['id_sede'],
                        $asignacionesActualizadas['sedes']
                    ),
                    'modulos_permitidos' => array_map(
                        static fn ($modulo) => $modulo['codigo'],
                        $asignacionesActualizadas['modulos']
                    ),
                    'colegios_disponibles' => array_map(
                        static fn (array $colegio): array => [
                            'id_colegio' => (int) $colegio['id_colegio'],
                            'nombre' => $colegio['nombre'],
                        ],
                        $asignacionesActualizadas['colegios']
                    ),
                    'sedes_disponibles' => array_map(
                        static fn (array $sede): array => [
                            'id_sede' => (int) $sede['id_sede'],
                            'nombre' => $sede['nombre'],
                            'id_colegio' => (int) $sede['id_colegio'],
                        ],
                        $asignacionesActualizadas['sedes']
                    ),
                ]));
                Session::set('context', [
                    'id_colegio' => $primerColegio['id_colegio'] ?? ($usuarioSesion['id_colegio'] ?? null),
                    'id_sede' => $primerSede['id_sede'] ?? ($usuarioSesion['id_sede'] ?? null),
                ]);
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
