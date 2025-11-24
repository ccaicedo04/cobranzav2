# SDK oficial de Twilio

Esta carpeta está preparada para alojar el paquete oficial [`twilio-php`](https://github.com/twilio/twilio-php).

Si tu entorno no puede instalar dependencias con Composer, descarga el `.zip` de la librería y copia el contenido de `src/Twilio`
aquí dentro, conservando la estructura `vendor/twilio/sdk/src/Twilio/...`.

Puedes versionar estos archivos directamente o simplemente desplegarlos en tu servidor. El sistema detectará automáticamente la
clase `Twilio\Rest\Client` cuando el SDK esté presente y en caso contrario utilizará el modo HTTP de respaldo.
