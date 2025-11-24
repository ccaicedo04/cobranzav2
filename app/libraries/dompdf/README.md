# DOMPDF standalone

Coloca aquí el archivo `dompdf-3.1.4.zip` descargado desde https://github.com/dompdf/dompdf/releases. Si ya tienes la carpeta `dompdf/` completa, puedes pegarla dentro de este mismo directorio (`app/libraries/dompdf/dompdf/`).

Al iniciar la aplicación, `Core\\DompdfSetup` descomprime automáticamente el ZIP (sin importar si está directamente en `app/libraries/dompdf/` o dentro de `app/libraries/dompdf/dompdf/`), mueve el contenido al nivel correcto y carga el autoload oficial (`autoload.inc.php`). Si ya se encuentra instalado, sólo garantiza que existan `lib/fonts` y `lib/cache`.

Si el ZIP se descomprime en un subdirectorio (`dompdf-3.1.4/` o `dompdf/`), el instalador moverá el contenido para que la ruta final sea:

```
app/libraries/dompdf/
    autoload.inc.php
    src/
    lib/
        fonts/
        cache/
```

Si faltan las extensiones `zip`, `gd` o `mbstring`, habilítalas en tu `php.ini` para evitar PDFs en blanco.
