<?php
$title = 'Plantillas oficiales';
$pageTitle = 'Formatos y ayudas';
$breadcrumbs = 'Parametrización / Plantillas';
include __DIR__ . '/../../_partials/header.php';

$plantillas = [
    [
        'nombre' => 'Carga masiva de estudiantes y deudas',
        'descripcion' => 'Formato estandarizado para cargar estudiantes, responsables y obligaciones en un solo paso.',
        'actualizacion' => 'Abril 2025',
        'ruta' => \Core\Helpers::baseUrl('plantillas/plantilla_carga_masiva.php'),
    ],
    [
        'nombre' => 'Manual de estructura CSV',
        'descripcion' => 'Guía rápida para transformar reportes propios a la estructura oficial del sistema.',
        'actualizacion' => 'Marzo 2025',
        'ruta' => null,
    ],
    [
        'nombre' => 'Checklist de alistamiento',
        'descripcion' => 'Lista de verificación previa a cada cargue masivo con responsables, fechas y validaciones.',
        'actualizacion' => 'Marzo 2025',
        'ruta' => null,
    ],
];
?>
<div class="card" style="margin-bottom:18px;">
    <h3>Descargas disponibles</h3>
    <p class="small">Mantén tus cargues consistentes con la estructura oficial y versiona tus procesos internos.</p>
    <table class="table">
        <thead>
            <tr><th>Formato</th><th>Descripción</th><th>Última actualización</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($plantillas as $plantilla): ?>
                <tr>
                    <td><?= htmlspecialchars($plantilla['nombre']) ?></td>
                    <td><?= htmlspecialchars($plantilla['descripcion']) ?></td>
                    <td><?= htmlspecialchars($plantilla['actualizacion']) ?></td>
                    <td>
                        <?php if ($plantilla['ruta']): ?>
                            <a class="btn sm" href="<?= $plantilla['ruta'] ?>">Descargar</a>
                        <?php else: ?>
                            <span class="tag">Disponible bajo solicitud</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="grid grid-2">
    <div class="card">
        <h3>Buenas prácticas para cargues</h3>
        <ul class="small" style="margin:0;padding-left:18px;line-height:1.7;">
            <li>Valida que los códigos de estudiante sean únicos y coincidan con el responsable financiero asignado.</li>
            <li>Incluye fechas en formato ISO (AAAA-MM-DD) para evitar rechazos por regionalización.</li>
            <li>Antes de un cargue masivo, genera un respaldo en <strong>Reportes &gt; Cartera</strong> para comparar saldos.</li>
            <li>Registra en auditoría quién ejecuta cada importación para mantener trazabilidad completa.</li>
        </ul>
    </div>
    <div class="card">
        <h3>Solicitar nuevos formatos</h3>
        <p class="small">¿Necesitas adaptar un formato a la realidad de tu colegio o integrar otros sistemas?</p>
        <p class="small">Envía tu requerimiento desde el módulo de <strong>Comunicaciones</strong> utilizando el asunto “Plantillas” o escribe a <a href="mailto:soporte@cobranza.edu">soporte@cobranza.edu</a>. Nuestro equipo entregará la personalización en un máximo de 48 horas hábiles.</p>
        <div style="margin-top:12px;display:flex;gap:10px;">
            <a class="btn" href="index.php?route=comunicaciones">Registrar solicitud</a>
            <a class="btn secondary" href="index.php?route=carga-masiva">Ir a cargue masivo</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
