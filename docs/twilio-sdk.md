# Instalación manual del SDK de Twilio

Si tu entorno no puede ejecutar `composer install`, puedes cargar el SDK oficial de Twilio de forma manual para habilitar todas las funcionalidades en tiempo real.

1. Descarga el archivo `.zip` de la versión estable más reciente desde el repositorio oficial: <https://github.com/twilio/twilio-php/releases>.
2. Descomprime el contenido y ubica la carpeta `twilio-php-*/src/Twilio`.
3. Crea la ruta `vendor/twilio/sdk/src/` dentro del proyecto (respeta las mayúsculas/minúsculas).
4. Copia la carpeta `Twilio` descomprimida dentro de `vendor/twilio/sdk/src/` y asegúrate de que exista el archivo `vendor/twilio/sdk/src/Twilio/autoload.php`.
5. (Opcional) Si recibes actualizaciones frecuentes, conserva también la carpeta `vendor/twilio/sdk/lib/` y cualquier otro recurso incluido en el `.zip` para mantener completa la librería.

El proyecto detectará automáticamente la clase `Twilio\Rest\Client` siempre que el autoloader `vendor/twilio/sdk/src/Twilio/autoload.php` esté presente. En caso contrario, el sistema seguirá utilizando el modo HTTP directo para enviar mensajes y descargar adjuntos.
