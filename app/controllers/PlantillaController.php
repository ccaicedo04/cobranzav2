<?php

namespace App\Controllers;

use Core\Controller;
use Core\Helpers;
use Core\Session;

class PlantillaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!Session::get('user')) {
            Helpers::redirect('index.php?route=auth/login');
        }
        $this->requireModule('parametrizacion');
    }

    public function index(): void
    {
        $this->view('parametrizacion/plantillas/index', []);
    }
}
