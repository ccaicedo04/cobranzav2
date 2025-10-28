<?php

namespace Core;

use RuntimeException;

class SimpleXlsx
{
    public static function download(string $filename, array $headers, array $rows, array $options = []): void
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('La extensión ZipArchive es requerida para generar XLSX.');
        }

        $headers = array_values($headers);
        $rows = array_map(static fn ($row) => array_values((array) $row), $rows);
        $maxColumns = count($headers);
        foreach ($rows as $row) {
            $maxColumns = max($maxColumns, count($row));
        }

        if ($maxColumns === 0) {
            $maxColumns = 1;
            $headers = ['Reporte'];
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        if ($tempFile === false) {
            throw new RuntimeException('No fue posible crear un archivo temporal para el XLSX.');
        }

        $zip = new \ZipArchive();
        $opened = $zip->open($tempFile, \ZipArchive::OVERWRITE | \ZipArchive::CREATE);
        if ($opened !== true) {
            @unlink($tempFile);
            throw new RuntimeException('No fue posible inicializar el contenedor XLSX. Código: ' . $opened);
        }

        $widths = [];
        $optionWidths = $options['widths'] ?? [];
        for ($i = 0; $i < $maxColumns; $i++) {
            $width = $optionWidths[$i] ?? null;
            if ($width !== null) {
                $width = (float) $width * 1.2;
            } else {
                $width = 12 + ($i < count($headers) ? strlen((string) $headers[$i]) * 0.4 : 0);
            }
            $widths[$i] = max(8.0, min(60.0, $width));
        }

        $sheetData = self::buildSheetXml($headers, $rows, $maxColumns, $widths);
        $created = gmdate('Y-m-d\TH:i:s\Z');

        $zip->addFromString('[Content_Types].xml', self::contentTypes());
        $zip->addFromString('_rels/.rels', self::rels());
        $zip->addFromString('docProps/app.xml', self::appProps());
        $zip->addFromString('docProps/core.xml', self::coreProps($created));
        $zip->addFromString('xl/workbook.xml', self::workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetData);
        $zip->close();

        $binary = file_get_contents($tempFile);
        @unlink($tempFile);
        if ($binary === false) {
            throw new RuntimeException('No fue posible leer el archivo XLSX generado.');
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($binary));
        echo $binary;
        exit;
    }

    private static function buildSheetXml(array $headers, array $rows, int $columns, array $widths): string
    {
        $rowIndex = 1;
        $rowsXml = [];
        $rowsXml[] = self::buildRow($rowIndex++, $headers, true, $columns);
        foreach ($rows as $row) {
            $rowsXml[] = self::buildRow($rowIndex++, $row, false, $columns);
        }

        $lastColumn = self::columnLetter($columns - 1) . max(1, $rowIndex - 1);
        $colsXml = [];
        for ($i = 0; $i < $columns; $i++) {
            $colLetter = $i + 1;
            $width = number_format($widths[$i] ?? 12.0, 2, '.', '');
            $colsXml[] = '<col min="' . $colLetter . '" max="' . $colLetter . '" width="' . $width . '" customWidth="1" />';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<dimension ref="A1:' . $lastColumn . '" />'
            . '<sheetViews><sheetView workbookViewId="0" tabSelected="1" /></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15" />'
            . '<cols>' . implode('', $colsXml) . '</cols>'
            . '<sheetData>' . implode('', $rowsXml) . '</sheetData>'
            . '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3" />'
            . '</worksheet>';
    }

    private static function buildRow(int $index, array $values, bool $header, int $columns): string
    {
        $cells = [];
        for ($i = 0; $i < $columns; $i++) {
            $cellRef = self::columnLetter($i) . $index;
            $value = $values[$i] ?? '';
            $text = self::xmlEscape((string) $value);
            $style = $header ? ' s="1"' : '';
            $cells[] = '<c r="' . $cellRef . '" t="inlineStr"' . $style . '><is><t xml:space="preserve">' . $text . '</t></is></c>';
        }

        return '<row r="' . $index . '">' . implode('', $cells) . '</row>';
    }

    private static function columnLetter(int $index): string
    {
        $index += 1;
        $letters = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = intdiv($index - 1, 26);
        }

        return $letters;
    }

    private static function xmlEscape(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
        return str_replace("\n", '&#10;', $value);
    }

    private static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml" />'
            . '<Default Extension="xml" ContentType="application/xml" />'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" />'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" />'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" />'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" />'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml" />'
            . '</Types>';
    }

    private static function rels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml" />'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml" />'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml" />'
            . '</Relationships>';
    }

    private static function appProps(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"'
            . ' xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>cobranzav2</Application>'
            . '<DocSecurity>0</DocSecurity>'
            . '<ScaleCrop>false</ScaleCrop>'
            . '<HeadingPairs><vt:vector size="2" baseType="variant">'
            . '<vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant>'
            . '<vt:variant><vt:i4>1</vt:i4></vt:variant>'
            . '</vt:vector></HeadingPairs>'
            . '<TitlesOfParts><vt:vector size="1" baseType="lpstr">'
            . '<vt:lpstr>Reporte</vt:lpstr>'
            . '</vt:vector></TitlesOfParts>'
            . '</Properties>';
    }

    private static function coreProps(string $created): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"'
            . ' xmlns:dc="http://purl.org/dc/elements/1.1/"'
            . ' xmlns:dcterms="http://purl.org/dc/terms/"'
            . ' xmlns:dcmitype="http://purl.org/dc/dcmitype/"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>Plataforma de Cobranzas</dc:creator>'
            . '<cp:lastModifiedBy>Plataforma de Cobranzas</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $created . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $created . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private static function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<fileVersion appName="Calc" />'
            . '<workbookPr date1904="false" />'
            . '<sheets><sheet name="Reporte" sheetId="1" r:id="rId1" /></sheets>'
            . '</workbook>';
    }

    private static function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml" />'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml" />'
            . '</Relationships>';
    }

    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="11" /><color theme="1" /><name val="Calibri" /><family val="2" /></font>'
            . '<font><b /><sz val="11" /><color theme="1" /><name val="Calibri" /><family val="2" /></font>'
            . '</fonts>'
            . '<fills count="2">'
            . '<fill><patternFill patternType="none" /></fill>'
            . '<fill><patternFill patternType="gray125" /></fill>'
            . '</fills>'
            . '<borders count="1"><border><left /><right /><top /><bottom /><diagonal /></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" /></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" />'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" />'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0" /></cellStyles>'
            . '</styleSheet>';
    }
}
