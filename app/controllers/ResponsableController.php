<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\ComunicacionModel;
use App\Models\DeudaModel;
use App\Models\EstudianteModel;
use App\Models\PagoModel;
use App\Models\ResponsableModel;
use App\Models\SedeModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ResponsableController extends Controller
{
    /** @var ResponsableModel */
    private $responsables;
    /** @var EstudianteModel */
    private $estudiantes;
    /** @var DeudaModel */
    private $deudas;
    /** @var PagoModel */
    private $pagos;
    /** @var ComunicacionModel */
    private $comunicaciones;
    /** @var SedeModel */
    private $sedes;
    /** @var ColegioModel */
    private $colegios;
    /** @var AuditoriaModel */
    private $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('cobranzas');

        $this->responsables = new ResponsableModel();
        $this->estudiantes = new EstudianteModel();
        $this->deudas = new DeudaModel();
        $this->pagos = new PagoModel();
        $this->comunicaciones = new ComunicacionModel();
        $this->sedes = new SedeModel();
        $this->colegios = new ColegioModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index()
    {
        $filtros = [
            'numero_documento' => $_GET['documento'] ?? null,
            'estado' => $_GET['estado'] ?? null,
        ];

        $responsables = $this->responsables->conContexto(array_filter($filtros));
        $this->view('responsables/index', [
            'responsables' => $responsables,
            'filtros' => $filtros,
        ]);
    }

    public function create()
    {
        $usuario = Session::get('user');
        $sedes = $this->sedes->conColegio();
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        }
        $this->view('responsables/form', [
            'sedes' => $sedes,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect('index.php?route=responsables');
        }

        if (!Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=responsables');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $idColegio = $_POST['id_colegio'] ?? $tenant['id_colegio'];
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = $tenant['id_colegio'];
        }
        $data = [
            'id_colegio' => $idColegio,
            'id_sede' => $_POST['id_sede'] ?? $tenant['id_sede'],
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'tipo_documento' => $_POST['tipo_documento'] ?? '',
            'numero_documento' => $_POST['numero_documento'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo',
            'eliminado' => 0,
        ];

        $id = $this->responsables->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'modulo' => 'responsables',
            'accion' => 'crear',
            'detalle' => 'Creación de responsable ' . $data['nombre_completo'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=responsables/detalle&id=' . $id);
    }

    public function edit()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $responsable = $this->responsables->find($id);
        if (!$responsable) {
            Helpers::redirect('index.php?route=responsables');
        }

        $usuario = Session::get('user');
        $sedes = $this->sedes->conColegio();
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        }
        $this->view('responsables/form', [
            'responsable' => $responsable,
            'sedes' => $sedes,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect('index.php?route=responsables');
        }

        if (!Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=responsables');
        }

        $id = (int) ($_POST['id_responsable'] ?? 0);
        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $idColegio = $_POST['id_colegio'] ?? $tenant['id_colegio'];
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = $tenant['id_colegio'];
        }

        $data = [
            'id_colegio' => $idColegio,
            'id_sede' => $_POST['id_sede'] ?? $tenant['id_sede'],
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'tipo_documento' => $_POST['tipo_documento'] ?? '',
            'numero_documento' => $_POST['numero_documento'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $this->responsables->update($id, $data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'modulo' => 'responsables',
            'accion' => 'actualizar',
            'detalle' => 'Actualización de responsable: ' . ($data['nombre_completo'] ?: ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=responsables/detalle&id=' . $id);
    }

    public function delete()
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            Helpers::redirect('index.php?route=responsables');
        }

        if (!Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=responsables');
        }

        $usuario = Session::get('user');
        $tenant = Helpers::tenantContext();
        $actual = $this->responsables->find($id);
        $this->responsables->delete($id);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $tenant['id_colegio'],
            'id_sede' => $tenant['id_sede'],
            'modulo' => 'responsables',
            'accion' => 'eliminar',
            'detalle' => 'Eliminación de responsable: ' . ($actual['nombre_completo'] ?? ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=responsables');
    }

    public function detalle()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $listado = $this->responsables->conContexto(['id_responsable' => $id]);
        $responsable = $listado[0] ?? null;
        if (!$responsable) {
            Helpers::redirect('index.php?route=responsables');
        }

        $estudiantes = $this->estudiantes->all(['id_responsable' => $id]);
        $deudas = $this->deudas->porResponsable($id);
        $pagos = $this->pagos->porResponsable($id);
        $comunicaciones = $this->comunicaciones->all(['id_responsable' => $id]);

        $this->view('responsables/detalle', [
            'responsable' => $responsable,
            'estudiantes' => $estudiantes,
            'deudas' => $deudas,
            'pagos' => $pagos,
            'comunicaciones' => $comunicaciones,
        ]);
    }
}
