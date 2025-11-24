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
        $this->limpiarBuffers();

        $htmlPreparado = trim((string) $html);
        if ($htmlPreparado === '') {
            $htmlPreparado = $this->htmlDesdeDocumento($documento);
        }

        if ($this->dompdfDisponible()) {
            try {
                $this->descargarConDompdf($htmlPreparado, $filename, $inline);

                return;
            } catch (Throwable $throwable) {
                // Si DOMPDF falla, continuamos con SimplePdf para no entregar un archivo en blanco.
            }
        }

        SimplePdf::downloadTable($filename, $documento, $inline);
    }

    /**
     * @return bool
     */
    private function dompdfDisponible()
    {
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
        $this->limpiarBuffers();

        $this->asegurarDirectoriosDompdf();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('fontDir', $this->dompdfFontsDir());
        $options->set('fontCache', $this->dompdfFontsDir());
        $options->set('tempDir', $this->dompdfCacheDir());

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

    /**
     * @return void
     */
    private function limpiarBuffers()
    {
        if (function_exists('ob_get_level')) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }
    }

    private function htmlDesdeDocumento(array $documento): string
    {
        $columnas = $documento['columns'] ?? [];
        $rows = $documento['rows'] ?? [];
        if (!$columnas) {
            $columnas = [['campo' => 'contenido', 'etiqueta' => 'Contenido']];
        }
        if (!$rows) {
            $rows = [['No hay información para los filtros aplicados.']];
        }

        ob_start();
        ?>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <style>
                body { font-family: Arial, sans-serif; margin: 24px; }
                h1 { margin: 0 0 8px; font-size: 18px; color: #0b3b77; }
                table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 11px; }
                th { background: #0b3b77; color: #fff; padding: 8px; text-align: left; }
                td { border: 1px solid #e5e7eb; padding: 8px; }
                tr:nth-child(even) td { background: #f9fafb; }
            </style>
        </head>
        <body>
            <h1><?= htmlspecialchars($documento['title'] ?? 'Reporte') ?></h1>
            <table>
                <thead>
                <tr>
                    <?php foreach ($columnas as $columna): ?>
                        <th><?= htmlspecialchars($columna['etiqueta'] ?? '') ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $fila): ?>
                    <tr>
                        <?php foreach ($fila as $celda): ?>
                            <td><?= htmlspecialchars((string) $celda, ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php

        return (string) ob_get_clean();
    }

    private function asegurarDirectoriosDompdf(): void
    {
        $fontsDir = $this->dompdfFontsDir();
        $cacheDir = $this->dompdfCacheDir();

        if (!is_dir($fontsDir)) {
            mkdir($fontsDir, 0755, true);
        }

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
    }

    private function dompdfFontsDir(): string
    {
        return dirname(__DIR__, 1) . '/libraries/dompdf/lib/fonts';
    }

    private function dompdfCacheDir(): string
    {
        return dirname(__DIR__, 1) . '/libraries/dompdf/lib/cache';
    }
}
