<?php
$title = 'Pagos';
$pageTitle = 'Pagos';
$breadcrumbs = 'Cobranzas / Pagos';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <div class="toolbar">
        <a class="btn" href="index.php?route=pagos/create">Registrar pago</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Estudiante</th>
                <th>Valor</th>
                <th>Método</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagos as $pago): ?>
                <tr>
                    <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                    <td><?= htmlspecialchars($pago['estudiante_nombre'] ?? $pago['id_estudiante']) ?></td>
                    <td>$ <?= number_format($pago['valor_total'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                    <td><?= htmlspecialchars($pago['referencia']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($pagos)): ?>
                <tr><td colspan="5">No hay pagos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
