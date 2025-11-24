<?php

namespace Core;

use Throwable;

class SimplePdf
{
    /**
     * @return void
     */
    public static function download(string $filename, array $lines)
    {
        $document = [
            'title' => 'Reporte',
            'subtitle' => '',
            'columns' => [
                ['campo' => 'contenido', 'etiqueta' => 'Contenido', 'ancho' => 100],
            ],
            'rows' => array_map(static fn ($line) => [(string) $line], $lines),
            'meta' => [
                'Generado' => date('Y-m-d H:i'),
                'Registros' => count($lines),
            ],
            'filters' => [],
            'summary' => [],
        ];

        self::downloadTable($filename, $document);
    }

    /**
     * @return void
     */
    public static function downloadTable(string $filename, array $document, bool $inline = false)
    {
        if (function_exists('ob_get_level')) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        try {
            $pdf = self::renderTable($document);
        } catch (Throwable $throwable) {
            $pdf = '';
        }

        if ($pdf === '' || strncmp($pdf, '%PDF', 4) !== 0) {
            $mensaje = 'No se pudo construir el PDF con los datos suministrados.';
            if (isset($throwable)) {
                $mensaje .= ' ' . $throwable->getMessage();
            }
            $pdf = self::fallbackPdf($mensaje);
        }

        header('Content-Type: application/pdf');
        header(($inline ? 'Content-Disposition: inline; filename="' : 'Content-Disposition: attachment; filename="') . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private static function renderTable(array $document): string
    {
        if (empty($document['columns'])) {
            $document['columns'] = [
                ['campo' => 'contenido', 'etiqueta' => 'Contenido', 'ancho' => 100],
            ];
        }

        $pageWidth = 595.0;
        $pageHeight = 842.0;
        $left = 50.0;
        $tableWidth = $pageWidth - ($left * 2);
        $headerHeight = 50.0;
        $rowHeight = 20.0;
        $padding = 6.0;

        $columns = $document['columns'] ?? [];
        $rows = $document['rows'] ?? [];
        $meta = $document['meta'] ?? [];
        $filters = $document['filters'] ?? [];
        $summary = $document['summary'] ?? [];

        $columnUnits = 0.0;
        foreach ($columns as $column) {
            $columnUnits += (float) ($column['ancho'] ?? 20);
        }
        if ($columnUnits <= 0) {
            $columnUnits = count($columns) * 20 ?: 20;
        }
        $columnWidths = [];
        foreach ($columns as $column) {
            $unit = (float) ($column['ancho'] ?? 20);
            $columnWidths[] = $tableWidth * $unit / $columnUnits;
        }

        $contents = [];
        $headerY = $pageHeight - 80.0;
        $titleColor = [0.96, 0.98, 1.0];

        $contents[] = 'q';
        $contents[] = '0.09 0.24 0.45 rg';
        $contents[] = self::rect($left, $headerY, $tableWidth, $headerHeight, 'f');
        $contents[] = 'Q';

        $contents[] = 'BT';
        $contents[] = sprintf('/F2 %.1f Tf', 18.0);
        $contents[] = sprintf('%.3f %.3f %.3f rg', $titleColor[0], $titleColor[1], $titleColor[2]);
        $contents[] = self::tm($left + 12.0, $headerY + $headerHeight - 18.0);
        $contents[] = '(' . self::escape((string) ($document['title'] ?? '')) . ') Tj';
        $contents[] = 'ET';

        if (!empty($document['subtitle'])) {
            $contents[] = 'BT';
            $contents[] = '/F1 11 Tf';
            $contents[] = sprintf('%.3f %.3f %.3f rg', $titleColor[0], $titleColor[1], $titleColor[2]);
            $contents[] = self::tm($left + 12.0, $headerY + 12.0);
            $contents[] = '(' . self::escape((string) $document['subtitle']) . ') Tj';
            $contents[] = 'ET';
        }

        $cursorY = $headerY - 24.0;
        $lineHeight = 14.0;

        foreach ($meta as $label => $value) {
            $contents[] = 'BT';
            $contents[] = '0 0 0 rg';
            $contents[] = '/F1 11 Tf';
            $contents[] = self::tm($left, $cursorY);
            $contents[] = '(' . self::escape($label . ': ' . $value) . ') Tj';
            $contents[] = 'ET';
            $cursorY -= $lineHeight;
        }

        if ($filters) {
            $cursorY -= 4.0;
            foreach ($filters as $filterLine) {
                $contents[] = 'BT';
                $contents[] = '0.25 0.33 0.47 rg';
                $contents[] = '/F1 10 Tf';
                $contents[] = self::tm($left, $cursorY);
                $contents[] = '(' . self::escape($filterLine) . ') Tj';
                $contents[] = 'ET';
                $cursorY -= $lineHeight;
            }
        }

        $cursorY -= 10.0;
        $headerRowY = $cursorY;

        $contents[] = 'q';
        $contents[] = '0.87 0.92 0.98 rg';
        $contents[] = self::rect($left, $headerRowY - $rowHeight, $tableWidth, $rowHeight, 'f');
        $contents[] = 'Q';

        $labelY = $headerRowY - 6.0;
        $offsetX = $left;
        foreach ($columns as $index => $column) {
            $text = $column['etiqueta'] ?? '';
            $contents[] = 'BT';
            $contents[] = '0 0 0 rg';
            $contents[] = '/F2 11 Tf';
            $contents[] = self::tm($offsetX + $padding, $labelY);
            $contents[] = '(' . self::escape($text) . ') Tj';
            $contents[] = 'ET';
            $offsetX += $columnWidths[$index] ?? 0;
        }

        $contents[] = 'q';
        $contents[] = '0 0 0 RG 0.6 w';
        $contents[] = self::rect($left, $headerRowY - $rowHeight, $tableWidth, $rowHeight, 'S');
        $contents[] = 'Q';

        $cursorY = $headerRowY - $rowHeight;
        if (!$rows) {
            $contents[] = 'BT';
            $contents[] = '0.45 0.45 0.45 rg';
            $contents[] = '/F1 11 Tf';
            $contents[] = self::tm($left + $padding, $cursorY - 6.0);
            $contents[] = '(' . self::escape('No hay información para los filtros aplicados.') . ') Tj';
            $contents[] = 'ET';
            $cursorY -= $rowHeight;
        } else {
            foreach ($rows as $rowIndex => $row) {
                $rowY = $cursorY - $rowHeight;
                $contents[] = 'q';
                $contents[] = ($rowIndex % 2 === 0) ? '0.96 0.97 0.99 rg' : '0.99 0.99 0.99 rg';
                $contents[] = self::rect($left, $rowY, $tableWidth, $rowHeight, 'f');
                $contents[] = 'Q';

                $contents[] = 'q';
                $contents[] = '0.85 0.85 0.85 RG 0.4 w';
                $contents[] = self::rect($left, $rowY, $tableWidth, $rowHeight, 'S');
                $contents[] = 'Q';

                $textY = $rowY + $rowHeight - 6.0;
                $offsetX = $left;
                foreach ($columns as $index => $column) {
                    $value = $row[$index] ?? '';
                    $fitted = self::fitText((string) $value, $columnWidths[$index] ?? 60);
                    $contents[] = 'BT';
                    $contents[] = '0 0 0 rg';
                    $contents[] = '/F1 10 Tf';
                    $contents[] = self::tm($offsetX + $padding, $textY);
                    $contents[] = '(' . self::escape($fitted) . ') Tj';
                    $contents[] = 'ET';
                    $offsetX += $columnWidths[$index] ?? 0;
                }

                $cursorY -= $rowHeight;
            }
        }

        if ($summary) {
            $cursorY -= 12.0;
            foreach ($summary as $label => $value) {
                $contents[] = 'BT';
                $contents[] = '0 0 0 rg';
                $contents[] = '/F2 11 Tf';
                $contents[] = self::tm($left, $cursorY);
                $contents[] = '(' . self::escape($label . ': ' . $value) . ') Tj';
                $contents[] = 'ET';
                $cursorY -= $lineHeight;
            }
        }

        $footerY = 40.0;
        $contents[] = 'BT';
        $contents[] = '0.35 0.35 0.35 rg';
        $contents[] = '/F1 10 Tf';
        $contents[] = self::tm($left, $footerY);
        $contents[] = '(' . self::escape('Desarrollado por: Technology and Innovation') . ') Tj';
        $contents[] = 'ET';

        $stream = implode("\n", $contents) . "\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>',
            '<< /Length ' . strlen($stream) . ' >>\nstream\n' . $stream . "\nendstream\n",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
        ];

        $buffer = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($buffer);
            $buffer .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xrefPos = strlen($buffer);
        $buffer .= 'xref\n0 ' . (count($objects) + 1) . "\n";
        $buffer .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $buffer .= sprintf("%010d 00000 n \n", $offset);
        }
        $buffer .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>\n';
        $buffer .= 'startxref\n' . $xrefPos . "\n%%EOF";

        return $buffer;
    }

    private static function fallbackPdf(string $mensaje): string
    {
        $contenido = 'BT /F1 12 Tf 50 800 Td (' . self::escape($mensaje) . ') Tj ET';
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length ' . strlen($contenido) . ' >>\nstream\n' . $contenido . "\nendstream\n",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $buffer = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($buffer);
            $buffer .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xrefPos = strlen($buffer);
        $buffer .= 'xref\n0 ' . (count($objects) + 1) . "\n";
        $buffer .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $buffer .= sprintf("%010d 00000 n \n", $offset);
        }
        $buffer .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>\n';
        $buffer .= 'startxref\n' . $xrefPos . "\n%%EOF";

        return $buffer;
    }

    private static function rect(float $x, float $y, float $w, float $h, string $mode): string
    {
        return sprintf('%.2f %.2f %.2f %.2f re %s', $x, $y, $w, $h, $mode);
    }

    private static function tm(float $x, float $y): string
    {
        return sprintf('1 0 0 1 %.2f %.2f Tm', $x, $y);
    }

    private static function fitText(string $text, float $width): string
    {
        $maxChars = max(1, (int) floor($width / 5.5));
        if (function_exists('mb_strimwidth')) {
            return mb_strimwidth($text, 0, $maxChars, '…', 'UTF-8');
        }

        $truncated = substr($text, 0, $maxChars);
        if (strlen($text) > strlen($truncated)) {
            $truncated = substr($truncated, 0, max(0, $maxChars - 1)) . '…';
        }

        return $truncated;
    }

    private static function escape(string $text): string
    {
        $text = preg_replace("/[\r\n]+/", ' ', $text);
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }
        $text = preg_replace('/[^\x20-\x7E]/', '?', $text);

        return $text;
    }
}
