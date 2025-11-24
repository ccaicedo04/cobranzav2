<?php

namespace App\Controllers;

use App\Models\PlantillaModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class PlantillaController extends Controller
{
    /** @var PlantillaModel */
    private $plantillas;

    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('parametrizacion');

        $this->plantillas = new PlantillaModel();
    }

    public function index(): void
    {
        $plantillas = $this->plantillas->all([], ['order' => "estado DESC, nombre"]);
        $this->view('parametrizacion/plantillas/index', [
            'plantillas' => $plantillas,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->view('parametrizacion/plantillas/form', [
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $plantilla = $this->plantillas->find($id);
        if (!$plantilla) {
            Helpers::redirect('index.php?route=plantillas');
        }

        $this->view('parametrizacion/plantillas/form', [
            'plantilla' => $plantilla,
            'token' => Helpers::csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->persist();
    }

    public function update(): void
    {
        $this->persist((int) ($_POST['id_plantilla'] ?? 0));
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=plantillas');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            Helpers::redirect('index.php?route=plantillas');
        }

        $this->plantillas->update($id, [
            'estado' => 'inactivo',
            'eliminado' => 1,
            'actualizado_por' => Session::get('user')['id_usuario'] ?? null,
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
        ]);

        Helpers::redirect('index.php?route=plantillas');
    }

    private function persist(int $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::validateCsrf($_POST['_token'] ?? '')) {
            Helpers::redirect('index.php?route=plantillas');
        }

        $usuario = Session::get('user');
        $contexto = Helpers::tenantContext();
        $idColegio = $contexto['id_colegio'] ?? ($usuario['id_colegio'] ?? null);
        if (!$idColegio) {
            Helpers::redirect('index.php?route=plantillas');
        }

        $variables = trim((string) ($_POST['variables'] ?? ''));
        $variables = $variables !== '' ? implode(',', array_filter(array_map('trim', preg_split('/[,\n]+/', $variables)))) : '';

        $data = [
            'id_colegio' => $idColegio,
            'nombre' => $_POST['nombre'] ?? '',
            'canal' => $_POST['canal'] ?? 'email',
            'descripcion' => $_POST['descripcion'] ?? '',
            'asunto_default' => $_POST['asunto_default'] ?? null,
            'cuerpo_html' => $_POST['cuerpo_html'] ?? '',
            'variables' => $variables,
            'estado' => $_POST['estado'] ?? 'activo',
            'eliminado' => 0,
            'actualizado_por' => $usuario['id_usuario'],
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            $this->plantillas->update($id, $data);
        } else {
            $data['creado_por'] = $usuario['id_usuario'];
            $this->plantillas->create($data);
        }

        Helpers::redirect('index.php?route=plantillas');
    }
}
