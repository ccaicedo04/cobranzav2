<?php
$title = 'Auditoría';
$pageTitle = 'Auditoría de usuarios';
$breadcrumbs = 'Administración / Auditoría';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card">
    <table class="table">
        <thead><tr><th>Fecha</th><th>Usuario</th><th>Colegio</th><th>Sede</th><th>Módulo</th><th>Acción</th><th>Detalle</th><th>IP</th></tr></thead>
        <tbody>
            <?php foreach ($registros as $registro): ?>
                <tr>
                    <td><?= htmlspecialchars($registro['fecha_registro']) ?></td>
                    <td><?= htmlspecialchars($registro['usuario_nombre'] ?? ('ID ' . $registro['id_usuario'])) ?></td>
                    <td><?= htmlspecialchars($registro['colegio_nombre'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($registro['sede_nombre'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($registro['modulo']) ?></td>
                    <td><?= htmlspecialchars($registro['accion']) ?></td>
                    <td><?= htmlspecialchars($registro['detalle']) ?></td>
                    <td><?= htmlspecialchars($registro['ip']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($registros)): ?>
                <tr><td colspan="8">No hay registros de auditoría.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
