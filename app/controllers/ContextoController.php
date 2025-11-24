<?php

namespace App\Controllers;

use App\Models\ColegioModel;
use App\Models\SedeModel;
use Core\Controller;
use Core\Helpers;
use Core\Session;

class ContextoController extends Controller
{
    /** @var ColegioModel */
    private $colegios;
    /** @var SedeModel */
    private $sedes;

    public function __construct()
    {
        parent::__construct();
        $this->colegios = new ColegioModel();
        $this->sedes = new SedeModel();
    }

    public function actualizar(): void
    {
        $token = $_POST['_token'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals((string) Session::get('contexto_token'), (string) $token)) {
            Helpers::redirect('index.php');
        }

        $usuario = Session::get('user');
        if (!$usuario) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $idColegio = $_POST['id_colegio'] ?? '';
        $idSede = $_POST['id_sede'] ?? '';

        $idColegio = $idColegio !== '' ? (int) $idColegio : null;
        $idSede = $idSede !== '' ? (int) $idSede : null;

        if (!empty($usuario['colegios_permitidos']) && $idColegio && !in_array($idColegio, $usuario['colegios_permitidos'], true)) {
            $idColegio = null;
        }

        if (!empty($usuario['sedes_permitidas']) && $idSede && !in_array($idSede, $usuario['sedes_permitidas'], true)) {
            $idSede = null;
        }

        if ($idSede && $idColegio === null) {
            $sede = $this->sedes->porIds([$idSede])[0] ?? null;
            $idColegio = $sede['id_colegio'] ?? null;
        }

        Session::set('context', [
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
        ]);

        $datosUsuario = Session::get('user');
        if ($datosUsuario) {
            $colegioNombre = $datosUsuario['colegio_nombre'];
            $sedeNombre = $datosUsuario['sede_nombre'];

            if ($idColegio) {
                $colegio = $this->colegios->porIds([$idColegio])[0] ?? null;
                $colegioNombre = $colegio['nombre'] ?? $colegioNombre;
            } elseif (!empty($usuario['colegios_disponibles'])) {
                $colegioNombre = count($usuario['colegios_disponibles']) > 1 ? 'Colegios asignados' : ($usuario['colegios_disponibles'][0]['nombre'] ?? $colegioNombre);
            }

            if ($idSede) {
                $sede = $this->sedes->porIds([$idSede])[0] ?? null;
                $sedeNombre = $sede['nombre'] ?? $sedeNombre;
            } elseif (!empty($usuario['sedes_disponibles'])) {
                $sedeNombre = count($usuario['sedes_disponibles']) > 1 ? 'Sedes asignadas' : ($usuario['sedes_disponibles'][0]['nombre'] ?? $sedeNombre);
            }

            $fallbackColegio = $usuario['colegios_disponibles'][0]['id_colegio'] ?? ($usuario['colegios_permitidos'][0] ?? $datosUsuario['id_colegio']);
            $fallbackSede = $usuario['sedes_disponibles'][0]['id_sede'] ?? ($usuario['sedes_permitidas'][0] ?? $datosUsuario['id_sede']);
            $datosUsuario['id_colegio'] = $idColegio ?? $fallbackColegio;
            $datosUsuario['id_sede'] = $idSede ?? $fallbackSede;
            $datosUsuario['colegio_nombre'] = $colegioNombre;
            $datosUsuario['sede_nombre'] = $sedeNombre;
            Session::set('user', $datosUsuario);
        }

        Session::set('contexto_token', bin2hex(random_bytes(16)));

        $redirect = $this->resolveRedirect($_POST['redirect'] ?? null);
        Helpers::redirect($redirect);
    }

    private function resolveRedirect($candidate): string
    {
        if (!is_string($candidate) || $candidate === '') {
            return 'index.php';
        }

        if (strpos($candidate, '://') !== false) {
            return 'index.php';
        }

        $candidate = trim($candidate);
        if ($candidate === '') {
            return 'index.php';
        }

        $candidate = ltrim($candidate, '/');
        $pos = strpos($candidate, 'index.php');
        if ($pos !== false) {
            $candidate = substr($candidate, $pos);
        }

        if ($candidate === '' || !str_starts_with($candidate, 'index.php')) {
            return 'index.php';
        }

        return $candidate;
    }
}
