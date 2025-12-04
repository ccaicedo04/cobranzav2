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
    /** @var UsuarioModel */
    private $usuarios;
    /** @var SedeModel */
    private $sedes;
    /** @var AuditoriaModel */
    private $auditoria;
    /** @var ColegioModel */
    private $colegios;
    /** @var ModuloModel */
    private $modulos;

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

    public function index()
    {
        $usuario = Session::get('user');
        $restricciones = [];
        if (($usuario['rol'] ?? null) === 'admin_colegio') {
            $restricciones['colegios'] = $this->colegiosPermitidosSesion($usuario);
        }

        $lista = $this->usuarios->listadoConContexto($restricciones);
        if (($usuario['rol'] ?? null) === 'admin_colegio') {
            $lista = array_values(array_filter($lista, function (array $fila) use ($usuario): bool {
                if ((int) ($fila['id_usuario'] ?? 0) === (int) ($usuario['id_usuario'] ?? 0)) {
                    return true;
                }
                if (($fila['rol'] ?? '') === 'admin_global') {
                    return false;
                }
                $colegiosFila = $fila['permisos_colegios_array'] ?? [];
                if (!$colegiosFila && !empty($fila['id_colegio'])) {
                    $colegiosFila = [(int) $fila['id_colegio']];
                }
                $colegiosFila = array_map('intval', (array) $colegiosFila);
                $colegiosSesion = $this->colegiosPermitidosSesion($usuario);
                if (!$colegiosSesion) {
                    return false;
                }

                return (bool) array_intersect($colegiosFila, $colegiosSesion);
            }));
        }

        $contextoForm = $this->contextoSelecciones();
        $modulosDisponibles = $this->modulosAsignables((array) $usuario);
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
            'rolesDisponibles' => $this->rolesDisponibles(),
            'modulosPorDefecto' => $this->modulosPorDefecto((array) $usuario),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuarioSesion = Session::get('user');
        $contexto = $this->contextoSelecciones();
        $hayColegios = !empty($contexto['colegios']);
        $haySedes = !empty($contexto['sedes']);

        $rol = $_POST['rol'] ?? 'agente';
        $rol = $this->normalizarRolSegunSesion($rol, $usuarioSesion);
        $permisosColegio = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_colegios'] ?? []))));
        $permisosSede = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_sedes'] ?? []))));
        $permisosModulo = array_values(array_unique(array_filter($_POST['permisos_modulos'] ?? [])));

        $passwordPlano = $_POST['password'] ?? '';
        $passwordConfirmacion = $_POST['password_confirm'] ?? '';
        if ($passwordPlano === '' && $passwordConfirmacion === '') {
            $passwordPlano = '123456';
        } elseif ($passwordConfirmacion !== '' && $passwordPlano !== $passwordConfirmacion) {
            Helpers::redirect('index.php?route=usuarios');
        }

        if (strlen($passwordPlano) < 6) {
            $passwordPlano = '123456';
        }

        $permisosColegio = $this->limitarColegiosPorSesion($permisosColegio, $usuarioSesion, $rol);
        $permisosSede = $this->limitarSedesPorSesion($permisosSede, $usuarioSesion, $permisosColegio, $rol);
        $permisosModulo = $this->limitarModulosPorSesion($permisosModulo, $usuarioSesion, $rol);

        if ($rol === 'admin_colegio' && empty($permisosColegio) && $hayColegios) {
            $permisosColegio = [(int) $contexto['colegios'][0]['id_colegio']];
        }

        if ($rol === 'agente' && empty($permisosSede) && $haySedes) {
            $permisosSede = [(int) $contexto['sedes'][0]['id_sede']];
        }

        if ($rol === 'agente' && empty($permisosSede) && $haySedes) {
            Helpers::redirect('index.php?route=usuarios');
        }

        if ($rol === 'admin_colegio' && empty($permisosColegio) && $hayColegios) {
            Helpers::redirect('index.php?route=usuarios');
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
            'password_hash' => password_hash($passwordPlano, PASSWORD_DEFAULT),
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
            'detalle' => 'Creación de usuario: ' . ($data['nombre_completo'] ?: ($data['usuario'] ?? ('ID ' . $id))),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=usuarios');
    }

    public function detalle()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuario = $this->usuarios->detalle($id);
        if (!$usuario || !$this->puedeGestionarUsuario($usuario)) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $asignaciones = $usuario['asignaciones'];
        $colegiosIds = array_map(static fn ($colegio) => (int) $colegio['id_colegio'], $asignaciones['colegios']);
        $sedesIds = array_map(static fn ($sede) => (int) $sede['id_sede'], $asignaciones['sedes']);
        $modulos = array_map(static fn ($modulo) => $modulo['codigo'], $asignaciones['modulos']);

        $colegiosAsignados = $asignaciones['colegios'];
        $sedesAsignadas = $asignaciones['sedes'];
        $modulosDisponibles = $this->modulosAsignables((array) Session::get('user'));
        $contextoForm = $this->contextoSelecciones();

        $this->view('administracion/usuarios/detalle', [
            'usuarioDetalle' => $usuario,
            'colegios' => $colegiosAsignados,
            'sedes' => $sedesAsignadas,
            'modulos' => $modulos,
            'opcionesColegios' => $contextoForm['colegios'],
            'opcionesSedes' => $contextoForm['sedes'],
            'modulosDisponibles' => $modulosDisponibles,
            'rolesDisponibles' => $this->rolesDisponibles(),
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $id = (int) ($_POST['id_usuario'] ?? 0);
        if ($id <= 0) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $usuarioSesion = Session::get('user');
        $actual = $this->usuarios->detalle($id);
        if (!$actual || !$this->puedeGestionarUsuario($actual)) {
            Helpers::redirect('index.php?route=usuarios');
        }

        $rol = $_POST['rol'] ?? 'agente';
        $rol = $this->normalizarRolSegunSesion($rol, $usuarioSesion);
        $permisosColegio = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_colegios'] ?? []))));
        $permisosSede = array_values(array_unique(array_filter(array_map('intval', $_POST['permisos_sedes'] ?? []))));
        $permisosModulo = array_values(array_unique(array_filter($_POST['permisos_modulos'] ?? [])));

        $permisosColegio = $this->limitarColegiosPorSesion($permisosColegio, $usuarioSesion, $rol);
        $permisosSede = $this->limitarSedesPorSesion($permisosSede, $usuarioSesion, $permisosColegio, $rol);
        $permisosModulo = $this->limitarModulosPorSesion($permisosModulo, $usuarioSesion, $rol);

        if ($rol === 'agente' && empty($permisosSede)) {
            Helpers::redirect('index.php?route=usuarios/detalle&id=' . $id);
        }

        if ($rol === 'admin_colegio' && empty($permisosColegio)) {
            Helpers::redirect('index.php?route=usuarios/detalle&id=' . $id);
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
            'detalle' => 'Actualización de usuario: ' . ($data['nombre_completo'] ?: ($data['usuario'] ?? ('ID ' . $id))),
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

    private function rolesDisponibles(): array
    {
        $usuario = Session::get('user');
        $rol = $usuario['rol'] ?? null;
        $roles = [
            'admin_global' => 'Administrador Global',
            'admin_colegio' => 'Administrador Colegio',
            'agente' => 'Agente',
        ];

        if ($rol === 'admin_global') {
            return $roles;
        }

        if ($rol === 'admin_colegio') {
            return [
                'admin_colegio' => $roles['admin_colegio'],
                'agente' => $roles['agente'],
            ];
        }

        return ['agente' => $roles['agente']];
    }

    private function modulosAsignables(array $usuarioSesion): array
    {
        $modulos = $this->modulos->activos();
        if (($usuarioSesion['rol'] ?? null) === 'admin_global') {
            return $modulos;
        }

        $permitidos = array_map('strval', $usuarioSesion['modulos_permitidos'] ?? []);
        if (!$permitidos) {
            return array_values(array_filter($modulos, static fn (array $modulo): bool => $modulo['codigo'] === 'cobranzas'));
        }

        return array_values(array_filter(
            $modulos,
            static fn (array $modulo): bool => in_array($modulo['codigo'], $permitidos, true)
        ));
    }

    private function modulosPorDefecto(array $usuarioSesion): array
    {
        if (($usuarioSesion['rol'] ?? null) === 'admin_global') {
            return array_map(static fn ($modulo) => $modulo['codigo'], $this->modulos->activos());
        }

        $permitidos = $usuarioSesion['modulos_permitidos'] ?? [];
        if ($permitidos) {
            return array_map('strval', $permitidos);
        }

        return ['cobranzas'];
    }

    private function normalizarRolSegunSesion(string $rolSolicitado, array $usuarioSesion): string
    {
        $rol = strtolower($rolSolicitado);
        $rolSesion = $usuarioSesion['rol'] ?? null;

        if ($rolSesion === 'admin_global') {
            return in_array($rol, ['admin_global', 'admin_colegio', 'agente'], true) ? $rol : 'agente';
        }

        if ($rolSesion === 'admin_colegio') {
            return $rol === 'admin_colegio' ? 'admin_colegio' : 'agente';
        }

        return 'agente';
    }

    private function limitarColegiosPorSesion(array $colegios, array $usuarioSesion, string $rol): array
    {
        $colegios = array_values(array_unique(array_filter(array_map('intval', $colegios))));
        if (($usuarioSesion['rol'] ?? null) === 'admin_global') {
            return $colegios;
        }

        $permitidos = $this->colegiosPermitidosSesion($usuarioSesion);
        if (!$permitidos) {
            return $colegios;
        }

        if (!$colegios) {
            return $rol === 'admin_colegio' ? $permitidos : [];
        }

        $resultado = array_values(array_intersect($colegios, $permitidos));

        return $resultado ?: ($rol === 'admin_colegio' ? $permitidos : $resultado);
    }

    private function limitarSedesPorSesion(array $sedes, array $usuarioSesion, array $colegiosSeleccionados, string $rol): array
    {
        $sedes = array_values(array_unique(array_filter(array_map('intval', $sedes))));
        $infoSeleccionadas = [];
        if ($sedes) {
            foreach ($this->sedes->porIds($sedes) as $sede) {
                $infoSeleccionadas[(int) $sede['id_sede']] = (int) $sede['id_colegio'];
            }
            $sedes = array_keys($infoSeleccionadas);
        }

        if (($usuarioSesion['rol'] ?? null) === 'admin_global') {
            if (!$colegiosSeleccionados || !$infoSeleccionadas) {
                return $sedes;
            }
            return array_values(array_filter($sedes, static fn (int $idSede) => in_array($infoSeleccionadas[$idSede] ?? 0, $colegiosSeleccionados, true)));
        }

        $permitidas = $this->sedesPermitidasSesion($usuarioSesion);
        if (!$permitidas) {
            return [];
        }

        if (!$sedes) {
            $sedes = $permitidas;
        } else {
            $sedes = array_values(array_intersect($sedes, $permitidas));
        }

        if ($colegiosSeleccionados) {
            $info = $this->sedes->porIds($sedes);
            $sedes = [];
            foreach ($info as $sede) {
                if (in_array((int) $sede['id_colegio'], $colegiosSeleccionados, true)) {
                    $sedes[] = (int) $sede['id_sede'];
                }
            }
        }

        return $sedes;
    }

    private function limitarModulosPorSesion(array $modulos, array $usuarioSesion, string $rol): array
    {
        $modulos = array_values(array_unique(array_filter(array_map('strval', $modulos))));

        if ($rol === 'agente') {
            $modulos = array_values(array_intersect($modulos ?: ['cobranzas'], ['cobranzas']));
        }

        if (($usuarioSesion['rol'] ?? null) === 'admin_global') {
            if (empty($modulos)) {
                return array_map(static fn ($modulo) => $modulo['codigo'], $this->modulos->activos());
            }

            return $modulos;
        }

        $permitidos = array_map('strval', $usuarioSesion['modulos_permitidos'] ?? []);
        if (!$permitidos) {
            return $rol === 'agente' ? ['cobranzas'] : $modulos;
        }

        if (!$modulos) {
            return $rol === 'agente'
                ? array_values(array_intersect($permitidos, ['cobranzas'])) ?: ['cobranzas']
                : $permitidos;
        }

        $resultado = array_values(array_intersect($modulos, $permitidos));

        if ($rol === 'agente' && empty($resultado)) {
            $resultado = array_values(array_intersect($permitidos, ['cobranzas'])) ?: ['cobranzas'];
        }

        return $resultado;
    }

    private function puedeGestionarUsuario(array $usuarioObjetivo): bool
    {
        $usuarioSesion = Session::get('user');
        if (($usuarioSesion['rol'] ?? null) === 'admin_global') {
            return true;
        }

        if (($usuarioSesion['rol'] ?? null) !== 'admin_colegio') {
            return false;
        }

        if ((int) ($usuarioSesion['id_usuario'] ?? 0) === (int) ($usuarioObjetivo['id_usuario'] ?? 0)) {
            return true;
        }

        if (($usuarioObjetivo['rol'] ?? '') === 'admin_global') {
            return false;
        }

        $colegiosObjetivo = $usuarioObjetivo['asignaciones']['colegios'] ?? [];
        if (!$colegiosObjetivo && !empty($usuarioObjetivo['id_colegio'])) {
            $colegiosObjetivo = [['id_colegio' => (int) $usuarioObjetivo['id_colegio']]];
        }

        $colegiosObjetivoIds = array_map(static fn (array $colegio) => (int) $colegio['id_colegio'], $colegiosObjetivo);
        $colegiosSesion = $this->colegiosPermitidosSesion($usuarioSesion);

        return (bool) array_intersect($colegiosSesion, $colegiosObjetivoIds);
    }

    private function colegiosPermitidosSesion(array $usuarioSesion): array
    {
        $permitidos = array_map('intval', $usuarioSesion['colegios_permitidos'] ?? []);
        if (!$permitidos && !empty($usuarioSesion['id_colegio'])) {
            $permitidos = [(int) $usuarioSesion['id_colegio']];
        }

        return $permitidos;
    }

    private function sedesPermitidasSesion(array $usuarioSesion): array
    {
        $permitidas = array_map('intval', $usuarioSesion['sedes_permitidas'] ?? []);
        if (!$permitidas && !empty($usuarioSesion['id_sede'])) {
            $permitidas = [(int) $usuarioSesion['id_sede']];
        }

        return $permitidas;
    }
}
