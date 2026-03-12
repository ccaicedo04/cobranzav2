<?php
$title = 'Detalle de sede';
$pageTitle = $sede['nombre'];
$breadcrumbs = 'Administración / Sedes / ' . $sede['nombre'];
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:1.2fr 1fr;">
    <div class="card">
        <h3>Resumen institucional</h3>
        <p><strong>Colegio:</strong> <?= htmlspecialchars($sede['colegio_nombre']) ?></p>
        <p><strong>NIT:</strong> <?= htmlspecialchars($sede['colegio_nit']) ?></p>
        <p><strong>Dirección:</strong> <?= htmlspecialchars($sede['direccion'] ?: 'Sin registrar') ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($sede['telefono'] ?: 'Sin registrar') ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($sede['correo'] ?: 'Sin registrar') ?></p>
        <p><strong>Estado:</strong> <span class="badge" style="background:<?= $sede['estado'] === 'activo' ? '#dcfce7' : '#fee2e2' ?>;color:<?= $sede['estado'] === 'activo' ? '#166534' : '#991b1b' ?>;">
            <?= strtoupper($sede['estado']) ?></span></p>
        <div class="actions" style="margin-top:16px;display:flex;gap:12px;">
            <a class="btn" href="index.php?route=sedes/edit&id=<?= $sede['id_sede'] ?>">Editar sede</a>
            <a class="btn secondary" href="index.php?route=sedes">Volver al listado</a>
        </div>
    </div>
    <div class="card">
        <h3>Indicadores rápidos</h3>
        <p><strong>Responsables asociados:</strong> <?= count($responsables) ?></p>
        <p><strong>Estudiantes activos:</strong> <?= count(array_filter($estudiantes, fn ($alumno) => $alumno['estado'] === 'activo')) ?></p>
        <p><strong>Total estudiantes:</strong> <?= count($estudiantes) ?></p>
    </div>
</div>
<div class="grid" style="grid-template-columns:1fr 1fr;">
    <div class="card">
        <h3>Responsables financieros</h3>
        <table class="table">
            <thead><tr><th>Nombre</th><th>Documento</th><th>Teléfono</th></tr></thead>
            <tbody>
                <?php foreach ($responsables as $responsable): ?>
                    <tr>
                        <td><?= htmlspecialchars($responsable['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($responsable['numero_documento']) ?></td>
                        <td><?= htmlspecialchars($responsable['telefono'] ?: 'Sin teléfono') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($responsables)): ?>
                    <tr><td colspan="3">No hay responsables asociados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Estudiantes</h3>
        <table class="table">
            <thead><tr><th>Nombre</th><th>Código</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?= htmlspecialchars($estudiante['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($estudiante['codigo_estudiante']) ?></td>
                        <td><?= htmlspecialchars(strtoupper($estudiante['estado'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($estudiantes)): ?>
                    <tr><td colspan="3">No hay estudiantes registrados en la sede.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
