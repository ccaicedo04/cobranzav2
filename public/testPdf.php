<?php
require_once __DIR__ . '/../app/libraries/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml('<h1>PDF funcionando correctamente</h1>');
$dompdf->render();
$dompdf->stream('prueba.pdf');
