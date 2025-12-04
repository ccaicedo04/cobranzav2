<?php
declare(strict_types=1);

use Core\SimpleZip;

require_once __DIR__ . '/../../core/SimpleZip.php';

$sheetCargaHeaders = [
    'Año cartera',
    'Curso',
    'Código estudiante',
    'Nombre estudiante',
    'Saldo pendiente',
    'Estado gestión',
    'Último seguimiento',
    'Compromiso',
    'Contacto autorizado',
    'Canal preferido',
    'Colegio',
    'Periodo',
    'Campaña',
    'Documento responsable',
    'Responsable financiero',
    'Correo responsable',
    'Teléfono responsable',
    'Sede',
    'Concepto deuda',
    'Fecha vencimiento',
];

$sheetCargaRows = [
    ['2024', '1B', '10176', 'Villalobos Chavista Samuel', '975125', 'CONTACTO DIRECTO', 'ENVÍO DE NOTIFICACIÓN', 'SI', 'SI', 'Email', 'Colegio Bilingüe Campestre Principado de Mónaco', 'Junio 2025', 'Cartera 2025', '80073011', 'Villalobos Muñoz Fernando', 'villalobos.munoz.fernando@familias-principado.edu.co', '+57 308 007 3011', 'Bogotá', 'Pensión escolar 2025', '2025-07-03'],
    ['2024', '5B', '10130', 'Castillo Figueroa Juana Valeria', '1540250', 'ESPERA DE RESPUESTA', 'PENDIENTE CONFIRMACIÓN', 'SI', 'SI', 'Email', 'Colegio Bilingüe Campestre Principado de Mónaco', 'Junio 2025', 'Cartera 2025', '79955368', 'Castillo Castro Jozep Evans', 'castillo.castro.jozep.evans@familias-principado.edu.co', '+57 307 995 5368', 'Bogotá', 'Pensión escolar 2025', '2025-07-04'],
    ['2024', '5A', '10119', 'Pineda Blanco Juan Esteban', '2224250', 'COMPROMISO DE PAGO', 'COMPROMISO DE PAGO', 'SI', 'SI', 'Email', 'Colegio Bilingüe Campestre Principado de Mónaco', 'Junio 2025', 'Cartera 2025', '79335279', 'Pineda Pachón Julio César', 'pineda.pachon.julio.cesar@familias-principado.edu.co', '+57 307 933 5279', 'Bogotá', 'Pensión escolar 2025', '2025-07-05'],
    ['2024', '4B', '10099', 'García Medina Gerónimo', '2379200', 'EN SEGUIMIENTO', 'ENVÍO DE NOTIFICACIÓN', 'SI', 'SI', 'Email', 'Colegio Bilingüe Campestre Principado de Mónaco', 'Junio 2025', 'Cartera 2025', '60288036', 'Medina Arévalo Angélica', 'medina.arevalo.angelica@familias-principado.edu.co', '+57 306 028 8036', 'Bogotá', 'Pensión escolar 2025', '2025-07-06'],
    ['2024', '4A', '10102', 'Maneiro Bolivar Yoneiker Alejandro', '985600', 'ESPERA DE RESPUESTA', 'PENDIENTE CONFIRMACIÓN', 'SI', 'SI', 'Email', 'Colegio Bilingüe Campestre Principado de Mónaco', 'Junio 2025', 'Cartera 2025', '5709969', 'Maneiro Fermín Manuel Valdemar', 'maneiro.fermin.manuel.valdemar@familias-principado.edu.co', '+57 300 570 9969', 'Bogotá', 'Pensión escolar 2025', '2025-07-07'],
];

$sheetReferenciaHeaders = ['Campo', 'Descripción', 'Valores sugeridos'];
$sheetReferenciaRows = [
    ['Año cartera', 'Periodo académico o fiscal del saldo registrado.', 'Ejemplo: 2024, 2025.'],
    ['Estado gestión', 'Etapa del proceso de cobranza según la última acción.', 'CONTACTO DIRECTO | EN SEGUIMIENTO | ESPERA DE RESPUESTA | COMPROMISO DE PAGO'],
    ['Último seguimiento', 'Última gestión realizada con el responsable.', 'ENVÍO DE NOTIFICACIÓN | COMPROMISO DE PAGO | RETIRADO DEL COLEGIO'],
    ['Compromiso', 'Define si existe acuerdo o promesa de pago vigente.', 'SI | NO'],
    ['Contacto autorizado', 'Permite identificar si se puede contactar directamente al responsable.', 'SI | NO'],
    ['Canal preferido', 'Medio recomendado para la comunicación.', 'Email | WhatsApp | Llamada'],
    ['Periodo', 'Nombre del periodo o mes asociado a la obligación.', 'Junio 2025, Abril 2025, etc.'],
    ['Campaña', 'Nombre del lote de cobranza cargado en el sistema.', 'Cartera 2025, Cartera Extraordinaria, etc.'],
    ['Concepto deuda', 'Concepto parametrizado en el sistema para la obligación.', 'Pensión escolar 2025 | Servicios complementarios'],
    ['Fecha vencimiento', 'Fecha límite para el pago de la obligación (AAAA-MM-DD).', 'Ejemplo: 2025-07-05'],
];

$sheets = [
    ['name' => 'Carga operativa', 'headers' => $sheetCargaHeaders, 'rows' => $sheetCargaRows],
    ['name' => 'Referencias', 'headers' => $sheetReferenciaHeaders, 'rows' => $sheetReferenciaRows],
];

$zip = new SimpleZip();

$created = gmdate('Y-m-d\TH:i:s\Z');
$zip->addFile('[Content_Types].xml', contentTypes(count($sheets)));
$zip->addFile('_rels/.rels', rels());
$zip->addFile('docProps/app.xml', appProps(array_column($sheets, 'name')));
$zip->addFile('docProps/core.xml', coreProps($created));
$zip->addFile('xl/workbook.xml', workbook($sheets));
$zip->addFile('xl/_rels/workbook.xml.rels', workbookRels(count($sheets)));
$zip->addFile('xl/styles.xml', styles());

foreach ($sheets as $index => $sheet) {
    $sheetXml = buildSheetXml($sheet['headers'], $sheet['rows'], $index === 0);
    $zip->addFile('xl/worksheets/sheet' . ($index + 1) . '.xml', $sheetXml);
}

$binary = $zip->toString();

$filename = 'plantilla_carga_masiva.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($binary));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $binary;
exit;

function buildSheetXml(array $headers, array $rows, bool $selected): string
{
    $maxColumns = count($headers);
    foreach ($rows as $row) {
        $maxColumns = max($maxColumns, count((array) $row));
    }

    if ($maxColumns === 0) {
        $maxColumns = 1;
        $headers = ['Campo'];
    }

    $widths = [];
    for ($i = 0; $i < $maxColumns; $i++) {
        $headerText = $headers[$i] ?? '';
        $width = 12 + strlen((string) $headerText) * 0.45;
        $widths[$i] = max(10.0, min(60.0, $width));
    }

    $rowsXml = [];
    $rowsXml[] = buildRow(1, $headers, true, $maxColumns);
    $rowIndex = 2;
    foreach ($rows as $row) {
        $rowsXml[] = buildRow($rowIndex++, (array) $row, false, $maxColumns);
    }

    $lastColumn = columnLetter($maxColumns - 1) . max(1, $rowIndex - 1);
    $colsXml = [];
    for ($i = 0; $i < $maxColumns; $i++) {
        $colLetter = $i + 1;
        $width = number_format($widths[$i], 2, '.', '');
        $colsXml[] = '<col min="' . $colLetter . '" max="' . $colLetter . '" width="' . $width . '" customWidth="1" />';
    }

    $sheetView = '<sheetView workbookViewId="0"';
    if ($selected) {
        $sheetView .= ' tabSelected="1"';
    }
    $sheetView .= ' />';

    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
        . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<dimension ref="A1:' . $lastColumn . '" />'
        . '<sheetViews>' . $sheetView . '</sheetViews>'
        . '<sheetFormatPr defaultRowHeight="15" />'
        . '<cols>' . implode('', $colsXml) . '</cols>'
        . '<sheetData>' . implode('', $rowsXml) . '</sheetData>'
        . '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3" />'
        . '</worksheet>';
}

function buildRow(int $index, array $values, bool $header, int $columns): string
{
    $cells = [];
    for ($i = 0; $i < $columns; $i++) {
        $cellRef = columnLetter($i) . $index;
        $value = $values[$i] ?? '';
        $text = xmlEscape((string) $value);
        $style = $header ? ' s="1"' : '';
        $cells[] = '<c r="' . $cellRef . '" t="inlineStr"' . $style . '><is><t xml:space="preserve">' . $text . '</t></is></c>';
    }

    return '<row r="' . $index . '">' . implode('', $cells) . '</row>';
}

function columnLetter(int $index): string
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

function xmlEscape(string $value): string
{
    $value = str_replace(["\r\n", "\r"], "\n", $value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    return str_replace("\n", '&#10;', $value);
}

function contentTypes(int $sheetCount): string
{
    $overrides = [
        '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" />',
        '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" />',
        '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" />',
        '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml" />',
    ];

    for ($i = 1; $i <= $sheetCount; $i++) {
        $overrides[] = '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" />';
    }

    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml" />'
        . '<Default Extension="xml" ContentType="application/xml" />'
        . implode('', $overrides)
        . '</Types>';
}

function rels(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml" />'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml" />'
        . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml" />'
        . '</Relationships>';
}

function appProps(array $sheetNames): string
{
    $count = count($sheetNames);
    $headingPairs = '<vt:vector size="2" baseType="variant">'
        . '<vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant>'
        . '<vt:variant><vt:i4>' . $count . '</vt:i4></vt:variant>'
        . '</vt:vector>';

    $titles = '<vt:vector size="' . $count . '" baseType="lpstr">';
    foreach ($sheetNames as $name) {
        $titles .= '<vt:lpstr>' . xmlEscape($name) . '</vt:lpstr>';
    }
    $titles .= '</vt:vector>';

    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"'
        . ' xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
        . '<Application>cobranzav2</Application>'
        . '<DocSecurity>0</DocSecurity>'
        . '<ScaleCrop>false</ScaleCrop>'
        . '<HeadingPairs>' . $headingPairs . '</HeadingPairs>'
        . '<TitlesOfParts>' . $titles . '</TitlesOfParts>'
        . '</Properties>';
}

function coreProps(string $created): string
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

function workbook(array $sheets): string
{
    $sheetTags = [];
    foreach ($sheets as $index => $sheet) {
        $name = htmlspecialchars($sheet['name'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $sheetTags[] = '<sheet name="' . $name . '" sheetId="' . ($index + 1) . '" r:id="rId' . ($index + 1) . '" />';
    }

    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
        . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<workbookPr date1904="false" />'
        . '<sheets>' . implode('', $sheetTags) . '</sheets>'
        . '</workbook>';
}

function workbookRels(int $sheetCount): string
{
    $relations = [];
    for ($i = 1; $i <= $sheetCount; $i++) {
        $relations[] = '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml" />';
    }

    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . implode('', $relations)
        . '</Relationships>';
}

function styles(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="2">'
        . '<font><sz val="11" /><color theme="1" /><name val="Calibri" /><family val="2" /></font>'
        . '<font><b /><sz val="11" /><color rgb="FFFFFFFF" /><name val="Calibri" /><family val="2" /></font>'
        . '</fonts>'
        . '<fills count="2">'
        . '<fill><patternFill patternType="none" /></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FF0F4C92" /><bgColor indexed="64" /></patternFill></fill>'
        . '</fills>'
        . '<borders count="1">'
        . '<border><left /><right /><top /><bottom /><diagonal /></border>'
        . '</borders>'
        . '<cellStyleXfs count="1">'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" />'
        . '</cellStyleXfs>'
        . '<cellXfs count="2">'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" />'
        . '<xf numFmtId="0" fontId="1" fillId="1" borderId="0" xfId="0" applyFont="1" applyFill="1" />'
        . '</cellXfs>'
        . '<cellStyles count="1">'
        . '<cellStyle name="Normal" xfId="0" builtinId="0" />'
        . '</cellStyles>'
        . '</styleSheet>';
}
