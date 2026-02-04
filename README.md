# Sistema de Cobranza Escolar v2025-10

Implementación MVC nativa en PHP 8.2 para la gestión integral de cartera multi-colegio y multi-sede. Incluye autenticación segura, panel con KPIs, módulos CRUD para responsables, estudiantes, deudas, pagos, acuerdos y comunicaciones, además de utilidades de parametrización, auditoría y reportes exportables.

## Visión general del producto (para Lovable)
**Nombre:** COBRANZA – Plataforma de Gestión de Cartera Escolar  
**Tipo:** SaaS multi-colegio / multi-sede  
**Objetivo:** Centralizar, automatizar y optimizar la gestión de cartera, pagos y comunicación con responsables financieros de instituciones educativas, reduciendo la morosidad y mejorando el recaudo mediante trazabilidad total y comunicación multicanal.  

**Usuarios objetivo**
- Administradores del sistema
- Personal administrativo del colegio
- Área financiera / cartera
- Coordinadores académicos (solo lectura)
- Super Admin (multi-colegio)

**Principales problemas que resuelve**
- Información de deudas dispersa
- Comunicación manual sin trazabilidad
- Falta de visibilidad del recaudo
- Cero control histórico de gestiones
- Dependencia de Excel
- Control limitado por roles
- Dificultad para crecer a múltiples colegios

**Arquitectura funcional**
- Gestión institucional
- Gestión financiera
- Gestión de personas
- Comunicación
- Analítica
- Seguridad y auditoría

## Módulos del sistema (core)
1. **Autenticación y control de acceso**: Login seguro, recuperación, redirección por rol, permisos y módulos habilitados.
2. **Dashboard ejecutivo**: KPIs, % morosidad, tendencias, top responsables, alertas visuales y filtros por colegio/sede.
3. **Gestión de colegios**: Wizard de creación, branding y configuración institucional.
4. **Gestión de sedes**: CRUD simple y filtro global por sede.
5. **Gestión de usuarios**: Roles, accesos por sede y estado activo/inactivo.
6. **Responsables financieros**: Vista de detalle con resumen financiero, estudiantes asociados e historial de gestiones multicanal.
7. **Estudiantes**: Historial financiero, pagos y navegación cruzada con responsable.
8. **Gestión de cartera**: Estados por semáforo, totales visibles y filtros avanzados.
9. **Pagos**: Registro manual, asociación a responsable/estudiante y trazabilidad.
10. **Comunicación multicanal**: WhatsApp/SMS/Correo con plantillas, confirmación y auditoría.
11. **Plantillas de mensajes**: Variables dinámicas y vista previa por canal.
12. **Carga masiva (Excel)**: Paso 1 subir, 2 validar, 3 confirmar con reporte de errores.
13. **Reportes**: Exportables a PDF/Excel con filtros avanzados.
14. **Auditoría**: Registro de acciones críticas y filtros por usuario/fecha.

## Prompt listo para Lovable (copiar y pegar)
```
Design a complete SaaS web application called “COBRANZA”, a multi-school debt collection platform for educational institutions.

The system manages schools, campuses, users, students, financial guardians, debts, payments, reports, audit logs, and multi-channel communication (WhatsApp, SMS, Email).

The UI must be modern, corporate, clean and scalable. Use a horizontal header layout, dashboards with charts, tables with filters, and clear call-to-action buttons.

Key screens:
- Login & password recovery
- Executive dashboard with KPIs and charts
- School and campus management
- User and role management
- Financial guardian detail view with debt summary, students list and communication actions
- Student detail view with financial history
- Debt and payment management
- Messaging center with templates and history
- Excel bulk upload wizard
- Reports export (PDF / Excel)
- Audit log viewer

All labels and texts must be in Spanish.

si se requiere colocar read o typescrip para mejorar el diseño hacerlo sin afectar a la funcionalidad

agregar mejor dashboard al sistemas todo profesional

realizarlo como lo haría lovable IA https://lovable.dev/
```

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
