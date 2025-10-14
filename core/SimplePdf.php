<?php

namespace Core;

class SimplePdf
{
    public static function download(string $filename, array $lines): void
    {
        $pdf = self::render($lines);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private static function render(array $lines): string
    {
        $clean = [];
        foreach ($lines as $line) {
            $clean[] = self::escape((string) $line);
        }

        $stream = "BT\n/F1 12 Tf\n14 TL\n72 800 Td\n";
        foreach ($clean as $line) {
            $stream .= '(' . $line . ") Tj\nT*\n";
        }
        $stream .= "ET\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length ' . strlen($stream) . ' >>\nstream\n' . $stream . "endstream",
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

    private static function escape(string $text): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        return preg_replace("/[\r\n]+/", ' ', $text);
    }
}
