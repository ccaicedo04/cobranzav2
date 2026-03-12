<?php
$title = 'Deudas';
$pageTitle = 'Deudas';
$breadcrumbs = 'Cobranzas / Deudas';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <div class="toolbar">
        <a class="btn" href="index.php?route=deudas/create">Nueva deuda</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Concepto</th>
                <th>Período</th>
                <th>Generación</th>
                <th>Saldo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deudas as $deuda): ?>
                <tr>
                    <td><?= htmlspecialchars($deuda['estudiante_nombre'] ?? $deuda['id_estudiante']) ?></td>
                    <td><?= htmlspecialchars($deuda['concepto_nombre'] ?? $deuda['id_concepto']) ?></td>
                    <td><?= htmlspecialchars($deuda['periodo_nombre'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($deuda['fecha_generacion']) ?></td>
                    <td>$ <?= number_format($deuda['saldo_actual'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($deuda['estado']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($deudas)): ?>
                <tr><td colspan="6">No hay deudas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
