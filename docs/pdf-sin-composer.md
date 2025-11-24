# 📄 README — Generación de PDF en PHP SIN COMPOSER (Sistema COBRANZA)

## 📌 1. Librería recomendada

Para proyectos en **PHP crudo sin Composer**, la librería sugerida es **DOMPDF (versión standalone)**.

* Funciona sin Composer
* Usa HTML + CSS para maquetar
* Es liviana y portable
* Encaja con el MVC existente (controladores + vistas)

---

## 📁 2. Instalación manual

1) **Descargar DOMPDF standalone** desde la sección de releases: https://github.com/dompdf/dompdf/releases (archivo `dompdf_X.X.X.zip`).

2) **Copiar los archivos** en el proyecto:

```
/app/libraries/dompdf/
    autoload.inc.php
    src/
```

3) **Registrar el autoload** (ya soportado por `core/Autoload.php`). No se necesita Composer ni vendor.

---

## 🚀 3. Uso dentro del MVC

Un ejemplo simple usando el nuevo `PdfService`:

```php
use App\Services\PdfService;

$pdf = new PdfService();
$html = '<h1>Hola DOMPDF</h1><p>Este PDF se genera sin Composer.</p>';
$documento = [
    'title' => 'Ejemplo',
    'subtitle' => 'Renderizado con DOMPDF',
    'columns' => [['campo' => 'contenido', 'etiqueta' => 'Contenido', 'ancho' => 100]],
    'rows' => [['Listo para usar']],
    'meta' => [],
    'filters' => [],
    'summary' => [],
];

$pdf->exportar([], [], $documento, $html, 'ejemplo.pdf', true);
```

*Si DOMPDF no está presente*, el servicio usará el generador nativo `Core\SimplePdf` para mantener las descargas funcionales.

---

## ✅ 4. Integración en Reportes

El controlador `ReporteController::exportPdf` usa `PdfService` automáticamente. Solo debes colocar DOMPDF en `app/libraries/dompdf/` para que la vista previa y las descargas salgan con HTML + CSS. Si falta, seguirá funcionando con el generador plano.

---

## ⚙️ 5. Requisitos

* PHP 7.4+
* Extensión `gd` habilitada para DOMPDF
* Sin Composer ni vendor: solo copiar la carpeta `dompdf` a `app/libraries/`

---

## 🧭 Estructura recomendada

```
/app
  /libraries
    /dompdf
  /services
    PdfService.php
  /controllers
    ReporteController.php
  /views
    /reportes
```

---

# 🚀 Listo para producción

Con estos pasos, el sistema COBRANZA puede generar reportes PDF, estados de cuenta y comprobantes **sin Composer** y manteniendo la portabilidad del proyecto.
