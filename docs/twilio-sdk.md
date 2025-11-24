# Instalación manual del SDK de Twilio

Si tu entorno no puede ejecutar `composer install`, puedes cargar el SDK oficial de Twilio de forma manual para habilitar todas las funcionalidades en tiempo real.

1. Descarga el archivo `.zip` de la versión estable más reciente desde el repositorio oficial: <https://github.com/twilio/twilio-php/releases>.
2. Descomprime el contenido y ubica la carpeta `twilio-php-*/src/Twilio`.
3. Crea la ruta `app/libraries/twilio/src/` dentro del proyecto (respeta las mayúsculas/minúsculas).
4. Copia la carpeta `Twilio` descomprimida dentro de `app/libraries/twilio/src/` y asegúrate de que exista el archivo `app/libraries/twilio/src/Twilio/autoload.php`.
5. (Opcional) Conserva también la carpeta `lib/` y cualquier otro recurso incluido en el `.zip` dentro de `app/libraries/twilio/` para mantener completa la librería.
6. Puedes versionar los archivos descargados si lo necesitas: el directorio `app/libraries/twilio/` está incluido en el repositorio, así que es posible subir el SDK oficial o simplemente dejarlo desplegado en tu servidor sin comprometer las actualizaciones de la rama.

> ℹ️ **Importante:** El repositorio ya incluye toda la lógica de envío y recepción mediante HTTP directo como respaldo. Sin embargo, para disfrutar de la experiencia completa (gestión de adjuntos, seguimiento avanzado, etc.) debes incorporar el SDK oficial siguiendo los pasos anteriores.
>
> Una vez copiado, el proyecto detectará automáticamente la clase `Twilio\\Rest\\Client` gracias al cargador definido en `core/Autoload.php`, siempre que exista el archivo `app/libraries/twilio/src/Twilio/autoload.php`. Si el SDK no está disponible, el sistema seguirá funcionando en modo HTTP como contingencia.
