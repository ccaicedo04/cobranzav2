<?php
declare(strict_types=1);

use App\Controllers\AcuerdoController;
use App\Controllers\AuditoriaController;
use App\Controllers\AuthController;
use App\Controllers\CargaController;
use App\Controllers\ColegioController;
use App\Controllers\ComunicacionController;
use App\Controllers\ConfiguracionController;
use App\Controllers\ContextoController;
use App\Controllers\DashboardController;
use App\Controllers\DeudaController;
use App\Controllers\EstudianteController;
use App\Controllers\PagoController;
use App\Controllers\ParametroController;
use App\Controllers\MantenimientoController;
use App\Controllers\PeriodoController;
use App\Controllers\PerfilController;
use App\Controllers\ReporteController;
use App\Controllers\ResponsableController;
use App\Controllers\SedeController;
use App\Controllers\UsuarioController;
use App\Controllers\ConceptoController;
use App\Controllers\PlantillaController;
use App\Controllers\TwilioWebhookController;
use Core\Autoload;
use Core\DompdfSetup;
use Core\Helpers;
use Core\Router;
use Core\Session;

require_once dirname(__DIR__) . '/core/Autoload.php';
Autoload::register();
DompdfSetup::bootstrap();

Session::start();
$route = $_GET['route'] ?? '/';
$route = trim($route, '/');
$route = $route === '' ? '/' : '/' . $route;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router = new Router();
$router->match(['GET', 'POST'], '/auth/login', [AuthController::class, 'login']);
$router->get('/auth/logout', [AuthController::class, 'logout']);
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/responsables', [ResponsableController::class, 'index']);
$router->get('/responsables/create', [ResponsableController::class, 'create']);
$router->post('/responsables/store', [ResponsableController::class, 'store']);
$router->get('/responsables/edit', [ResponsableController::class, 'edit']);
$router->post('/responsables/update', [ResponsableController::class, 'update']);
$router->post('/responsables/delete', [ResponsableController::class, 'delete']);
$router->get('/responsables/detalle', [ResponsableController::class, 'detalle']);

$router->get('/estudiantes', [EstudianteController::class, 'index']);
$router->get('/estudiantes/create', [EstudianteController::class, 'create']);
$router->post('/estudiantes/store', [EstudianteController::class, 'store']);
$router->get('/estudiantes/edit', [EstudianteController::class, 'edit']);
$router->post('/estudiantes/update', [EstudianteController::class, 'update']);
$router->post('/estudiantes/delete', [EstudianteController::class, 'delete']);
$router->get('/estudiantes/detalle', [EstudianteController::class, 'detalle']);

$router->get('/deudas', [DeudaController::class, 'index']);
$router->get('/deudas/create', [DeudaController::class, 'create']);
$router->post('/deudas/store', [DeudaController::class, 'store']);

$router->get('/pagos', [PagoController::class, 'index']);
$router->get('/pagos/create', [PagoController::class, 'create']);
$router->post('/pagos/store', [PagoController::class, 'store']);

$router->get('/acuerdos', [AcuerdoController::class, 'index']);
$router->post('/acuerdos/store', [AcuerdoController::class, 'store']);

$router->get('/comunicaciones', [ComunicacionController::class, 'index']);
$router->post('/comunicaciones/store', [ComunicacionController::class, 'store']);
$router->get('/comunicaciones/conversacion', [ComunicacionController::class, 'conversation']);

$router->get('/carga-masiva', [CargaController::class, 'index']);
$router->post('/carga-masiva/store', [CargaController::class, 'store']);

$router->get('/reportes', [ReporteController::class, 'index']);
$router->get('/reportes/export-excel', [ReporteController::class, 'exportExcel']);
$router->get('/reportes/export-pdf', [ReporteController::class, 'exportPdf']);

$router->get('/mantenimiento/limpiar-cobranzas', [MantenimientoController::class, 'limpiarCobranzas']);
$router->post('/mantenimiento/ejecutar-limpieza', [MantenimientoController::class, 'ejecutarLimpieza']);

$router->get('/colegios', [ColegioController::class, 'index']);
$router->post('/colegios/store', [ColegioController::class, 'store']);
$router->get('/colegios/edit', [ColegioController::class, 'edit']);
$router->post('/colegios/update', [ColegioController::class, 'update']);
$router->get('/colegios/detalle', [ColegioController::class, 'detalle']);

$router->get('/sedes', [SedeController::class, 'index']);
$router->post('/sedes/store', [SedeController::class, 'store']);
$router->get('/sedes/edit', [SedeController::class, 'edit']);
$router->post('/sedes/update', [SedeController::class, 'update']);
$router->get('/sedes/detalle', [SedeController::class, 'detalle']);

$router->get('/usuarios', [UsuarioController::class, 'index']);
$router->post('/usuarios/store', [UsuarioController::class, 'store']);
$router->get('/usuarios/detalle', [UsuarioController::class, 'detalle']);
$router->post('/usuarios/update', [UsuarioController::class, 'update']);

$router->get('/parametros', [ParametroController::class, 'index']);
$router->post('/parametros/store', [ParametroController::class, 'store']);
$router->post('/parametros/update', [ParametroController::class, 'update']);

$router->get('/conceptos', [ConceptoController::class, 'index']);
$router->post('/conceptos/store', [ConceptoController::class, 'store']);
$router->get('/conceptos/edit', [ConceptoController::class, 'edit']);
$router->post('/conceptos/update', [ConceptoController::class, 'update']);

$router->get('/periodos', [PeriodoController::class, 'index']);
$router->post('/periodos/store', [PeriodoController::class, 'store']);
$router->get('/periodos/edit', [PeriodoController::class, 'edit']);
$router->post('/periodos/update', [PeriodoController::class, 'update']);

$router->get('/plantillas', [PlantillaController::class, 'index']);
$router->get('/plantillas/create', [PlantillaController::class, 'create']);
$router->get('/plantillas/edit', [PlantillaController::class, 'edit']);
$router->post('/plantillas/store', [PlantillaController::class, 'store']);
$router->post('/plantillas/update', [PlantillaController::class, 'update']);
$router->post('/plantillas/delete', [PlantillaController::class, 'delete']);

$router->get('/auditoria', [AuditoriaController::class, 'index']);

$router->get('/configuracion', [ConfiguracionController::class, 'index']);
$router->post('/configuracion/store', [ConfiguracionController::class, 'store']);

$router->get('/perfil', [PerfilController::class, 'index']);
$router->post('/perfil/actualizar', [PerfilController::class, 'actualizar']);
$router->post('/contexto/actualizar', [ContextoController::class, 'actualizar']);

$router->match(['GET', 'POST'], '/webhooks/twilio', [TwilioWebhookController::class, 'incoming']);

try {
    $router->dispatch($method, $route);
} catch (\Throwable $throwable) {
    http_response_code(500);
    if ((require __DIR__ . '/../config/config.php')['app']['debug']) {
        echo '<pre>' . htmlspecialchars($throwable->getMessage()) . '\n' . $throwable->getTraceAsString() . '</pre>';
    } else {
        echo 'Ha ocurrido un error inesperado.';
    }
}
