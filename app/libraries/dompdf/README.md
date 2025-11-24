# DOMPDF manual (sin Composer)

Coloca aquí la versión standalone de DOMPDF descargada desde https://github.com/dompdf/dompdf/releases.
La estructura debe quedar exactamente así:

```
app/libraries/dompdf/
  autoload.inc.php
  src/
  lib/
    fonts/
    cache/
```

No se usa Composer ni autoloads personalizados. `public/index.php` cargará `autoload.inc.php` si existe.
