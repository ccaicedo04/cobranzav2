# Entrega completa de la aplicación COBRANZA Escolar

Este repositorio corresponde a la rama **work** (equivalente a la rama `completo` indicada por el cliente) y contiene todos los artefactos necesarios para que el sistema funcione sin depender de archivos externos.

## Contenido incluido
- **Aplicación PHP MVC**:
  - `app/controllers/`: controladores para autenticación, cartera, comunicación, reportes, configuración y cargue masivo.
  - `app/models/`: modelos con soporte multi-colegio/sede, incluyendo responsables financieros, estudiantes, deudas, pagos y auditoría.
  - `app/services/`: servicios auxiliares (por ejemplo `CargaPhidiasService` para el cargue de cartera desde Excel).
  - `app/views/`: vistas completas del panel administrativo y formularios asociados.
- **Núcleo y utilidades**:
  - `core/`: router, conexión PDO, helpers, sesiones y el lector `SimpleXlsxReader` embebido para XLSX.
- **Recursos públicos**:
  - `public/`: punto de entrada (`index.php`), assets, imágenes, pantallas, plantillas y carpeta `uploads/` (vacía por defecto, lista para recibir archivos subidos).
- **Datos y plantillas**:
  - `database/cobranza_escolar.sql`: volcado con estructura completa y datos demo.
  - `plantillas/` y `public/plantillas/`: ejemplos y generador dinámico de plantillas.
- **Documentación**:
  - `README.md`, `docs/README.txt`, guías para PDF y Twilio, además de este inventario.

## Cómo utilizar
1. Copia el proyecto en tu servidor (ej. `htdocs/cobranzav2`).
2. Importa `database/cobranza_escolar.sql` en MySQL/MariaDB.
3. Ajusta credenciales en `config/config.php`.
4. Ingresa a `public/index.php` con las credenciales demo detalladas en `docs/README.txt`.

No se han omitido carpetas ni archivos necesarios para la operación del sistema; todo lo requerido está versionado en este repositorio.
