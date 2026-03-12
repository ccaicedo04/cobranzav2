Sistema de Cobranza Escolar (COBRANZA v2025-10)
===============================================

Este proyecto contiene una implementación MVC nativa en PHP 8.2 para la gestión de cartera escolar multi-colegio/multi-sede.

Estructura principal:
- public/: punto de entrada y recursos estáticos.
- app/: controladores, modelos y vistas.
- core/: componentes del framework (router, base de datos, helpers, sesión).
- database/: scripts SQL y datos de ejemplo.
- plantillas/: documentación y especificaciones de formatos de carga.
- public/plantillas/: generadores dinámicos de plantillas descargables.

Para ejecutar localmente:
1. Copia la carpeta en tu entorno XAMPP (htdocs) o servidor Apache compatible.
2. Importa el archivo database/cobranza_escolar.sql en tu motor MariaDB/MySQL.
3. Ajusta credenciales en config/config.php según tu entorno.
4. Accede desde el navegador a http://localhost/cobranzav2/public/index.php.

Usuarios demo:
- admin / admin123 (Administrador global)
- admin1 / admin123 (Administrador Colegio San José)
- admin2 / admin123 (Administrador Colegio Nuestra Señora)
- agente1 / agente123 (Agente de cobranza)

La plantilla Excel para carga masiva se genera dinámicamente mediante public/plantillas/plantilla_carga_masiva.php, evitando almacenar binarios en el repositorio.

Notas de base de datos:
- Los permisos de colegio, sede y módulos se administran mediante tablas pivote (`usuario_colegio`, `usuario_sede`, `usuario_modulo`).
- El catálogo de módulos activos reside en `modulo_sistema`, permitiendo habilitar o deshabilitar vistas sin modificar código.
