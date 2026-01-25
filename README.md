# Sistema de Cobranza Escolar v2025-10

Implementación MVC nativa en PHP 8.2 para la gestión integral de cartera multi-colegio y multi-sede. Incluye autenticación segura, panel con KPIs, módulos CRUD para responsables, estudiantes, deudas, pagos, acuerdos y comunicaciones, además de utilidades de parametrización, auditoría y reportes exportables.

## Requisitos
- PHP 7.4+ (código PHP crudo, sin Composer)
- Extensiones PHP habilitadas: `pdo`, `pdo_mysql`, `openssl` y (opcional) `curl`/`mbstring` para mensajería y exportes. Para PDF HTML con DOMPDF activa también `dom`, `gd` y `mbstring`.
- MariaDB/MySQL 10.4+
- Servidor Apache (XAMPP recomendado)
- Sin Composer: el autoload propio (`core/Autoload.php`) carga tanto las clases del proyecto (usa rutas en minúscula como `app/controllers`, `app/models`, etc.) como los SDKs que copies manualmente en `app/libraries/` (ej. Twilio)

## Instalación rápida
1. Copia el proyecto en tu carpeta web (`htdocs` en XAMPP).
2. Importa `database/cobranza_escolar.sql` en tu motor de base de datos (el volcado incluido trae la estructura completa y datos demo para que todas las pantallas funcionen al abrir el proyecto).
3. Ajusta credenciales en `config/config.php` si es necesario. El sistema detecta la ruta base automáticamente desde `public/index.php`, pero puedes definir `app.base_url` si despliegas en un subdominio o carpeta distinta.
4. Accede a `http://localhost/cobranzav2/public/index.php` y utiliza las credenciales demo descritas en `docs/README.txt`.

## Estructura destacada
- `core/`: router, manejador PDO, helpers y manejo de sesiones.
- `app/controllers`: controladores para cada módulo.
- `app/models`: modelos con soporte multi-tenant (`id_colegio`, `id_sede`).
- `app/views`: vistas basadas en el diseño entregado.
- `plantillas/`: documentación de formatos para carga masiva.
- `public/plantillas/`: script que genera la plantilla Excel sin almacenar binarios.
- `docs/pdf-sin-composer.md`: guía detallada para integrar DOMPDF de forma manual (sin Composer) y evitar PDFs en blanco.

## Configuración de mensajes entrantes de WhatsApp
Para visualizar mensajes entrantes en el módulo de Comunicaciones y recibir notificaciones por correo:
1. En Twilio activa el sandbox de WhatsApp o usa un número aprobado.
2. En la consola de Twilio configura el **Webhook de mensajes entrantes** apuntando a:
   `https://TU_DOMINIO/cobranzav2/public/index.php?route=/webhooks/twilio`
3. En la sección **Configuración del colegio** completa:
   - Account SID y Auth Token de Twilio.
   - Número de WhatsApp remitente (formato E.164).
   - Content SID de la plantilla aprobada.
4. Configura un SMTP válido (host, usuario y contraseña). El sistema enviará un correo a ese usuario cada vez que llegue un mensaje entrante de WhatsApp.

## Licencia
Uso interno educativo.

## Entrega completa
Consulta `docs/ENTREGA_COMPLETA.md` para ver el inventario de carpetas, base de datos y guías incluidas en esta entrega.
