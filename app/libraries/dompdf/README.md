# DOMPDF standalone

Coloca aquí el archivo `dompdf-3.1.4.zip` descargado desde https://github.com/dompdf/dompdf/releases.

Al iniciar la aplicación, `Core\\DompdfSetup` descomprime automáticamente el ZIP en esta misma carpeta y carga el autoload oficial (`autoload.inc.php`). Si ya se encuentra instalado, sólo garantiza que existan `lib/fonts` y `lib/cache`.

Si el ZIP se descomprime en un subdirectorio (`dompdf-3.1.4/`), el instalador moverá el contenido para que la ruta final sea:

```
app/libraries/dompdf/
    autoload.inc.php
    src/
    lib/
        fonts/
        cache/
```

Si faltan las extensiones `zip`, `gd` o `mbstring`, habilítalas en tu `php.ini` para evitar PDFs en blanco.
