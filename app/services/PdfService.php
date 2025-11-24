<?php

namespace App\Services;

use Core\SimplePdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Throwable;

class PdfService
{
    /**
     * Genera y envía el PDF del reporte. Si DOMPDF está disponible (carpeta app/libraries/dompdf),
     * se usa para renderizar HTML; de lo contrario, se recurre al generador plano SimplePdf.
     *
     * @param array $config Configuración de columnas y textos del reporte
     * @param array $datos  Filas de datos asociadas al reporte
     * @param array $documento Estructura ya preparada para SimplePdf
     * @param string $html Vista HTML opcional ya renderizada para DOMPDF
     * @param string $filename Nombre del archivo de salida
     * @param bool $inline Si se debe mostrar en el navegador (true) o descargar (false)
     * @return void
     */
    public function exportar(array $config, array $datos, array $documento, $html, $filename, $inline = false)
    {
        if ($this->dompdfDisponible()) {
            $this->descargarConDompdf((string) $html, $filename, $inline);
            return;
        }

        SimplePdf::downloadTable($filename, $documento, $inline);
    }

    /**
     * @return bool
     */
    private function dompdfDisponible()
    {
        if (class_exists(Dompdf::class)) {
            return true;
        }

        $autoload = dirname(__DIR__, 1) . '/libraries/dompdf/autoload.inc.php';
        if (is_file($autoload)) {
            require_once $autoload;
        }

        return class_exists(Dompdf::class);
    }

    /**
     * @param string $html
     * @param string $filename
     * @param bool $inline
     * @return void
     */
    private function descargarConDompdf($html, $filename, $inline)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html ?: '<p>Sin contenido para mostrar.</p>');
        $dompdf->setPaper('A4', 'portrait');

        try {
            $dompdf->render();
        } catch (Throwable $throwable) {
            throw new Exception('No fue posible renderizar el PDF con DOMPDF: ' . $throwable->getMessage());
        }

        $dompdf->stream($filename, ['Attachment' => !$inline]);
        exit;
    }
}
