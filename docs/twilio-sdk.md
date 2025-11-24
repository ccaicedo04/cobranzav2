# Instalación manual del SDK de Twilio

Si tu entorno no puede ejecutar `composer install`, puedes cargar el SDK oficial de Twilio de forma manual para habilitar todas las funcionalidades en tiempo real.

1. Descarga el archivo `.zip` de la versión estable más reciente desde el repositorio oficial: <https://github.com/twilio/twilio-php/releases>.
2. Descomprime el contenido y ubica la carpeta `twilio-php-*/src/Twilio`.
3. Crea la ruta `vendor/twilio/sdk/src/` dentro del proyecto (respeta las mayúsculas/minúsculas).
4. Copia la carpeta `Twilio` descomprimida dentro de `vendor/twilio/sdk/src/` y asegúrate de que exista el archivo `vendor/twilio/sdk/src/Twilio/autoload.php`.
5. (Opcional) Conserva también la carpeta `vendor/twilio/sdk/lib/` y cualquier otro recurso incluido en el `.zip` para mantener completa la librería.
6. Puedes versionar los archivos descargados si lo necesitas: el directorio `vendor/` ya no está ignorado en `.gitignore`, así que es posible subir el SDK oficial al repositorio o simplemente dejarlo desplegado en tu servidor sin comprometer las actualizaciones de la rama.

> ℹ️ **Importante:** El repositorio ya incluye toda la lógica de envío y recepción mediante HTTP directo como respaldo. Sin embargo, para disfrutar de la experiencia completa (gestión de adjuntos, seguimiento avanzado, etc.) debes incorporar el SDK oficial siguiendo los pasos anteriores.
>
> Una vez copiado, el proyecto detectará automáticamente la clase `Twilio\\Rest\\Client` siempre que el autoloader `vendor/twilio/sdk/src/Twilio/autoload.php` esté presente. Si el SDK no está disponible, el sistema seguirá funcionando en modo HTTP como contingencia.
