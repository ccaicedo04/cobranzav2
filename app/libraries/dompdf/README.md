# DOMPDF standalone (sin Composer)

Coloca aquí el ZIP oficial desde https://github.com/dompdf/dompdf/releases
con la estructura exacta:

```
app/libraries/dompdf/
  autoload.inc.php
  src/
  lib/
    fonts/
    cache/
```

No utilices autoloaders propios ni Composer. `public/index.php` carga este
`autoload.inc.php` antes de despachar rutas. Activa las extensiones `dom`,
`gd` y `mbstring` en tu `php.ini` para evitar PDFs en blanco.
