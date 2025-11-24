# 📄 README — Integración Completa de DOMPDF en PHP Crudo (Sin Composer) para COBRANZA

Este documento resume cómo dejar DOMPDF funcionando sin Composer, evitar PDFs en blanco y preparar la estructura exacta que espera el sistema COBRANZA.

## 🚀 1. Requisitos de PHP

- PHP 7.4+
- Extensiones activas en `php.ini`: `dom`, `gd`, `mbstring`, `zip`
- `allow_url_fopen = On` para cargar imágenes remotas

## 📁 2. Estructura obligatoria

Copia el ZIP standalone desde https://github.com/dompdf/dompdf/releases (ej: `dompdf-3.1.4.zip`) en:

```
/app/libraries/dompdf/
    dompdf-3.1.4.zip
```

Al iniciar la app, `Core\DompdfSetup` descomprime automáticamente el ZIP, mueve los archivos al nivel correcto y crea `lib/fonts` y `lib/cache` si no existen. La ruta final queda:

```
/app/libraries/dompdf/
    autoload.inc.php
    src/
    lib/
        fonts/
        cache/
```

## 🔧 3. Autoload único

El proyecto carga **solo** el autoload oficial desde `Core\DompdfSetup`, que también descomprime el ZIP si es necesario:

```php
use Core\DompdfSetup;
DompdfSetup::bootstrap();
```

No uses autoloaders adicionales ni Composer.

## 🛠️ 4. Servicio PDF

El servicio nativo `app/services/PdfService.php` limpia buffers, usa DOMPDF cuando está disponible y cae en `Core\SimplePdf` si faltan archivos. Para generar un PDF con HTML:

```php
require_once __DIR__ . '/../app/services/PdfService.php';

$pdf = new \App\Services\PdfService();
$html = '<h1>Hola DOMPDF</h1><p>PDF sin Composer.</p>';
$pdf->generar($html, 'ejemplo.pdf');
```

## 🎯 5. Controlador de ejemplo

`ReporteController::exportPdf` ya llama a `PdfService`. Asegúrate de invocar la vista con `return true`:

```php
$html = $this->view('reportes/cartera', ['data' => $data], true);
$pdf->generar($html, 'reporte_cartera.pdf');
```

## 📝 6. Vista mínima de prueba

Archivo: `app/views/reportes/cartera.php`

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; }
        h1 { text-align: center; }
    </style>
</head>
<body>
<h1>Reporte de Cartera</h1>
<p>El PDF fue generado correctamente.</p>
</body>
</html>
```

## 🧪 7. Test manual

Archivo: `public/testPdf.php`

```php
<?php
require_once __DIR__ . '/../core/DompdfSetup.php';

use Core\DompdfSetup;
use Dompdf\Dompdf;
use Dompdf\Options;

DompdfSetup::bootstrap();
$html = '<h1>PDF funcionando correctamente</h1>';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('prueba.pdf');
```

Abrir en el navegador:
```
http://localhost/cobranzav2/public/testPdf.php
```

## ❗ 8. Cómo evitar PDFs en blanco

1. **Sin salida previa**: nada de `echo`, `var_dump` ni espacios antes de `<?php`. Usa `ob_start(); ... ob_end_clean();` antes de renderizar.
2. **Vista debe retornar HTML**: llama a `view('ruta', $data, true)`.
3. **Rutas válidas a imágenes/CSS** y `isRemoteEnabled = true`.
4. **UTF-8** en la cabecera: `<meta charset="UTF-8">`.
5. **Extensiones dom/gd/mbstring activas**.

Con esta configuración, los PDFs dejan de salir en blanco y el proyecto sigue 100% libre de Composer.
