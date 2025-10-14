<?php
$title = 'Reportes';
$pageTitle = 'Reportes de gestión';
$breadcrumbs = 'Reportes';
include __DIR__ . '/../_partials/header.php';
?>
<div class="grid grid-2">
    <div class="card">
        <h3>Cartera pendiente</h3>
        <p style="font-size:32px;font-weight:700;">$ <?= number_format($carteraPendiente ?? 0, 0, ',', '.') ?></p>
        <p class="small">Incluye deudas activas y acuerdos pendientes.</p>
    </div>
    <div class="card">
        <h3>Descargar reportes</h3>
        <p class="small">Genera reportes en formatos CSV y PDF listos para compartir.</p>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <a class="btn" href="index.php?route=reportes/export-excel">Top responsables (CSV)</a>
            <a class="btn secondary" href="index.php?route=reportes/export-pdf">Top responsables (PDF)</a>
        </div>
    </div>
</div>
<div class="card">
    <h3>Top responsables</h3>
    <table class="table">
        <thead><tr><th>Responsable</th><th>Total cartera</th></tr></thead>
        <tbody>
            <?php foreach ($topResponsables as $responsable): ?>
                <tr>
                    <td><?= htmlspecialchars($responsable['nombre_completo']) ?></td>
                    <td>$ <?= number_format($responsable['total'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($topResponsables)): ?>
                <tr><td colspan="2">No hay datos.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
