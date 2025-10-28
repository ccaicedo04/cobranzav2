<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;
use App\Models\ColegioModel;
use App\Models\PeriodoModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class PeriodoController extends Controller
{
    private PeriodoModel $periodos;
    private ColegioModel $colegios;
    private AuditoriaModel $auditoria;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('parametrizacion');

        $this->periodos = new PeriodoModel();
        $this->colegios = new ColegioModel();
        $this->auditoria = new AuditoriaModel();
    }

    public function index(): void
    {
        $usuario = Session::get('user');
        $periodos = $this->periodos->conColegio();
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        } elseif (!empty($usuario['id_colegio'])) {
            $colegios = $this->colegios->all(['id_colegio' => $usuario['id_colegio']]);
        }

        $this->view('parametrizacion/periodos/index', [
            'periodos' => $periodos,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=periodos');
        }

        $usuario = Session::get('user');
        $idColegio = $_POST['id_colegio'] ?? $usuario['id_colegio'] ?? null;
        if ($usuario['rol'] !== 'admin_global' && empty($idColegio)) {
            Helpers::redirect('index.php?route=periodos');
        }

        $data = [
            'id_colegio' => $usuario['rol'] === 'admin_global' ? $idColegio : $usuario['id_colegio'],
            'nombre' => $_POST['nombre'] ?? '',
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin' => $_POST['fecha_fin'] ?? null,
            'estado' => $_POST['estado'] ?? 'activo',
            'eliminado' => 0,
        ];

        $id = $this->periodos->create($data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'periodos',
            'accion' => 'crear',
            'detalle' => 'Creación de período: ' . ($data['nombre'] ?: ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=periodos');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $periodo = $this->periodos->find($id);
        if (!$periodo) {
            Helpers::redirect('index.php?route=periodos');
        }

        $usuario = Session::get('user');
        $colegios = [];
        if ($usuario['rol'] === 'admin_global') {
            $colegios = $this->colegios->all([], ['order' => 'nombre']);
        }

        $this->view('parametrizacion/periodos/form', [
            'periodo' => $periodo,
            'colegios' => $colegios,
            'usuario' => $usuario,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=periodos');
        }

        $id = (int) ($_POST['id_periodo'] ?? 0);
        if (!$id) {
            Helpers::redirect('index.php?route=periodos');
        }

        $usuario = Session::get('user');
        $idColegio = $_POST['id_colegio'] ?? $usuario['id_colegio'] ?? null;
        if ($usuario['rol'] !== 'admin_global') {
            $idColegio = $usuario['id_colegio'];
        }

        $data = [
            'id_colegio' => $idColegio,
            'nombre' => $_POST['nombre'] ?? '',
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin' => $_POST['fecha_fin'] ?? null,
            'estado' => $_POST['estado'] ?? 'activo',
        ];

        $this->periodos->update($id, $data);
        $this->auditoria->create([
            'id_usuario' => $usuario['id_usuario'],
            'id_colegio' => $usuario['id_colegio'],
            'id_sede' => $usuario['id_sede'],
            'modulo' => 'periodos',
            'accion' => 'actualizar',
            'detalle' => 'Actualización de período: ' . ($data['nombre'] ?: ('ID ' . $id)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'fecha_registro' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=periodos');
    }
}
