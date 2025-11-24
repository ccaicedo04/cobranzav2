# Sistema de Cobranza Escolar v2025-10

Implementación MVC nativa en PHP 8.2 para la gestión integral de cartera multi-colegio y multi-sede. Incluye autenticación segura, panel con KPIs, módulos CRUD para responsables, estudiantes, deudas, pagos, acuerdos y comunicaciones, además de utilidades de parametrización, auditoría y reportes exportables.

## Requisitos
- PHP 7.0+ (probado en entornos sin Composer)
- Extensiones PHP habilitadas: `pdo`, `pdo_mysql`, `openssl` y (opcional) `curl`/`mbstring` para mensajería y exportes
- MariaDB/MySQL 10.4+
- Servidor Apache (XAMPP recomendado)
- Sin Composer: el autoload propio (`core/Autoload.php`) carga tanto las clases del proyecto como los SDKs que copies manualmente en `app/libraries/` (ej. Twilio)

## Instalación rápida
1. Copia el proyecto en tu carpeta web (`htdocs` en XAMPP).
2. Importa `database/cobranza_escolar.sql` en tu motor de base de datos.
3. Ajusta credenciales en `config/config.php` si es necesario. El sistema detecta la ruta base automáticamente desde `public/index.php`, pero puedes definir `app.base_url` si despliegas en un subdominio o carpeta distinta.
4. Accede a `http://localhost/cobranzav2/public/index.php` y utiliza las credenciales demo descritas en `docs/README.txt`.

## Estructura destacada
- `core/`: router, manejador PDO, helpers y manejo de sesiones.
- `app/controllers`: controladores para cada módulo.
- `app/models`: modelos con soporte multi-tenant (`id_colegio`, `id_sede`).
- `app/views`: vistas basadas en el diseño entregado.
- `plantillas/`: documentación de formatos para carga masiva.
- `public/plantillas/`: script que genera la plantilla Excel sin almacenar binarios.

## Licencia
Uso interno educativo.
