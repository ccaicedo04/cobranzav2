# Plantillas de carga masiva

La plantilla oficial para la carga masiva se distribuye como un archivo Excel generado dinámicamente desde `public/plantillas/plantilla_carga_masiva.php`. El script entrega un libro con dos hojas:

- **Estudiantes**: columnas `codigo_estudiante`, `nombre_completo`, `grado`, `curso`, `id_responsable`.
- **Deudas**: columnas `codigo_estudiante`, `concepto`, `periodo`, `valor_inicial`, `saldo_actual`, `fecha_generacion`, `fecha_vencimiento`.

Puedes actualizar este documento si necesitas documentar cambios en la estructura requerida por los procesos de importación.
