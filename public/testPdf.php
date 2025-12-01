<?php
require_once __DIR__ . '/../core/DompdfSetup.php';

use Core\DompdfSetup;
use Dompdf\Dompdf;
use Dompdf\Options;

if (!DompdfSetup::bootstrap() || !class_exists(Dompdf::class)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'DOMPDF no está disponible. Copia dompdf-3.1.4.zip o la carpeta dompdf/ en app/libraries/dompdf/ y recarga esta página.';
    exit;
}

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml('<h1>PDF funcionando correctamente</h1>');
$dompdf->render();
$dompdf->stream('prueba.pdf');
