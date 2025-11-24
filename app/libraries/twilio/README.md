# SDK de Twilio sin Composer

Coloca aquí la carpeta `src/Twilio` del paquete oficial [`twilio-php`](https://github.com/twilio/twilio-php) cuando no puedas usar Composer.

## Pasos rápidos
1. Descarga el ZIP desde Packagist o GitHub (`Repository → Download ZIP`).
2. Descomprime y copia la carpeta `src/Twilio` dentro de `app/libraries/twilio/`.
3. Verifica que exista `app/libraries/twilio/src/Twilio/autoload.php`.

Con la nueva carga automática en `core/Autoload.php`, las clases del SDK (`Twilio\Rest\Client`, etc.) se resolverán automáticamente.
