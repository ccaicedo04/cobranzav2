<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\ConceptoModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ConceptoController extends Controller
{
    /** @var ConceptoModel */
    private $conceptos;
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
        $this->requireModule('parametrizacion');

        $this->conceptos = new ConceptoModel();
        $this->colegios = new ColegioModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $usuario = Session::get('user');
        $conceptos = $this->conceptos->conColegio();
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        } elseif (!empty($usuario['id_colegio'])) {
            $colegios = $this->colegios->all(['id_colegio' => $usuario['id_colegio']]);
        }

        $this->view('parametrizacion/conceptos/index', [
            'conceptos' => $conceptos,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=conceptos');
        }

        $usuario = Session::get('user');
        $idColegio = $_POST['id_colegio'] ?? $usuario['id_colegio'] ?? null;
        if ($usuario['rol'] !== 'admin_global' && empty($idColegio)) {
            Helpers::redirect('index.php?route=conceptos');
        }

        $data = [
            'id_colegio' => $usuario['rol'] === 'admin_global' ? $idColegio : $usuario['id_colegio'],
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'recurrente',
            'valor_base' => $_POST['valor_base'] ?? 0,
            'estado' => $_POST['estado'] ?? 'activo',
            'eliminado' => 0,
        ];

        $id = $this->conceptos->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'conceptos',
            'accion' => 'crear',
            'detalle' => 'Creación de concepto: ' . ($data['nombre'] ?: ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=conceptos');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $concepto = $this->conceptos->find($id);
        if (!$concepto) {
            Helpers::redirect('index.php?route=conceptos');
        }

        $usuario = Session::get('user');
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        }

        $this->view('parametrizacion/conceptos/form', [
            'concepto' => $concepto,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=conceptos');
        }

        $id = (int) ($_POST['id_concepto'] ?? 0);
        if (!$id) {
            Helpers::redirect('index.php?route=conceptos');
        }

        $usuario = Session::get('user');
        $idColegio = $_POST['id_colegio'] ?? $usuario['id_colegio'] ?? null;
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = $usuario['id_colegio'];
        }

        $data = [
            'id_colegio' => $idColegio,
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'recurrente',
            'valor_base' => $_POST['valor_base'] ?? 0,
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $this->conceptos->update($id, $data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'conceptos',
            'accion' => 'actualizar',
            'detalle' => 'Actualización de concepto: ' . ($data['nombre'] ?: ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=conceptos');
    }
}
