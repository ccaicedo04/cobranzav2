<?php

namespace Core;

use Core\Session;
use Core\Helpers;

abstract class Controller
{
    /** @var array */
    protected $data = [];

    public function __construct()
    {
        date_default_timezone_set((require __DIR__ . '/../config/config.php')['app']['timezone']);
        Session::start();
    }

    /**
     * @return void
     */
    protected function view(string $view, array $data = [])
    {
        $this->data = $data;
        extract($data, EXTR_SKIP);
        $viewPath = __DIR__ . '/../app/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Vista {$view} no encontrada");
        }

        require $viewPath;
    }

    /**
     * @return void
     */
    protected function requireRole(string ...$roles)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user['rol'], $roles, true)) {
            Helpers::redirect('index.php?route=auth/login');
        }
    }

    /**
     * @return void
     */
    protected function requireModule(string $module)
    {
        $user = Session::get('user');
        if (!$user) {
            Helpers::redirect('index.php?route=auth/login');
        }

        $modulos = $user['modulos_permitidos'] ?? [];
        if (!in_array($module, $modulos, true)) {
            Helpers::redirect('index.php');
        }
    }
}
