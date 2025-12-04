<?php

namespace App\Services;

use Core\DompdfSetup;
use Core\SimplePdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Throwable;

class PdfService
{
    /**
     * Genera un PDF a partir de HTML usando DOMPDF cuando está disponible,
     * con un fallback directo a SimplePdf para mantener la descarga funcional.
     *
     * @param string $html       Contenido HTML listo para renderizar.
     * @param string $nombre     Nombre del archivo de salida.
     * @param string $orientacion Orientación del papel (portrait|landscape).
     * @param bool   $inline     True para vista previa en navegador.
     * @param array|null $documento Fallback para SimplePdf en caso de no contar con DOMPDF.
     * @return void
     */
    public function generar($html, $nombre = 'documento.pdf', $orientacion = 'portrait', $inline = false, ?array $documento = null)
    {
        $this->limpiarBuffers();

        $contenido = trim((string) $html);
        if ($contenido === '' && $documento) {
            $contenido = $this->htmlDesdeDocumento($documento);
        }
        if ($contenido === '') {
            $contenido = '<p>Contenido no disponible para renderizar.</p>';
        }

        $rowCount = is_array($documento['rows'] ?? null) ? count($documento['rows']) : 0;
        $contenidoPesado = strlen($contenido) > 1_500_000 || $rowCount > 1200;

        if ($this->dompdfDisponible() && !$contenidoPesado) {
            @ini_set('memory_limit', '1024M');
            $this->asegurarDirectoriosDompdf();

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('tempDir', $this->dompdfCacheDir());
            $options->set('fontDir', $this->dompdfFontsDir());
            $options->set('fontCache', $this->dompdfFontsDir());
            $options->set('isHtml5ParserEnabled', true);
            $options->set('enable_font_subsetting', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($contenido);
            $dompdf->setPaper('A4', $orientacion);
            $dompdf->render();
            $dompdf->stream($nombre, ['Attachment' => !$inline]);
            exit;
        }

        $fallback = $documento ?: [
            'title' => 'Documento PDF',
            'subtitle' => '',
            'columns' => [['campo' => 'contenido', 'etiqueta' => 'Contenido', 'ancho' => 100]],
            'rows' => [[strip_tags($contenido)]],
            'meta' => [],
            'filters' => [],
            'summary' => [],
        ];

        SimplePdf::downloadTable($nombre, $fallback, $inline);
    }

    /**
     * Exporta la tabla estándar de reportes con HTML generado por la vista.
     *
     * @param array $config
     * @param array $datos
     * @param array $documento
     * @param string $html
     * @param string $filename
     * @param bool $inline
     * @return void
     */
    public function exportar(array $config, array $datos, array $documento, $html, $filename, $inline = false)
    {
        $this->limpiarBuffers();

        $htmlPreparado = trim((string) $html);
        if ($htmlPreparado === '') {
            $htmlPreparado = $this->htmlDesdeDocumento($documento);
        }

        $this->generar($htmlPreparado, $filename, 'portrait', $inline, $documento);
    }

    /**
     * @return bool
     */
    private function dompdfDisponible()
    {
        DompdfSetup::bootstrap();

        return class_exists(Dompdf::class);
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
                h1 { margin: 0 0 8px; font-size: 18px; color: #0b3b77; text-align: center; }
                p { color: #374151; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 11px; }
                th { background: #0b3b77; color: #fff; padding: 8px; text-align: left; }
                td { border: 1px solid #e5e7eb; padding: 8px; }
                tr:nth-child(even) td { background: #f9fafb; }
            </style>
        </head>
        <body>
            <h1><?= htmlspecialchars($documento['title'] ?? 'Reporte') ?></h1>
            <?php if (!empty($documento['subtitle'])): ?>
                <p><?= htmlspecialchars($documento['subtitle']) ?></p>
            <?php endif; ?>
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
