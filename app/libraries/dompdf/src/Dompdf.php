<?php

namespace Dompdf;

use Core\SimplePdf;
use Exception;
use Throwable;

class Dompdf
{
    /** @var Options */
    private $options;
    /** @var string */
    private $html = '';
    /** @var string */
    private $orientation = 'portrait';
    /** @var string */
    private $paper = 'A4';

    public function __construct(?Options $options = null)
    {
        $this->options = $options ?: new Options();
    }

    public function loadHtml($html)
    {
        $this->html = (string) $html;
    }

    public function setPaper($size, $orientation = 'portrait')
    {
        $this->paper = (string) $size;
        $this->orientation = (string) $orientation;
    }

    public function render()
    {
        // No-op: rendering deferred until stream() so we can reuse SimplePdf if needed.
        return null;
    }

    public function stream($filename, array $options = [])
    {
        $inline = !empty($options['Attachment']) ? !$options['Attachment'] : false;

        try {
            $text = trim(strip_tags($this->html));
            if ($text === '') {
                $text = 'Contenido no disponible para renderizar.';
            }

            $document = [
                'title' => 'Documento PDF',
                'subtitle' => sprintf('Papel: %s | Orientación: %s', $this->paper, $this->orientation),
                'columns' => [
                    ['campo' => 'contenido', 'etiqueta' => 'Contenido', 'ancho' => 100],
                ],
                'rows' => [[$text]],
                'meta' => [],
                'filters' => [],
                'summary' => [],
            ];

            SimplePdf::downloadTable($filename, $document, $inline);
        } catch (Throwable $throwable) {
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo 'No fue posible generar el PDF: ' . $throwable->getMessage();
            exit;
        }
    }
}
