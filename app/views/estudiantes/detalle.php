<?php
$title = 'Detalle estudiante';
$pageTitle = $estudiante['nombre_completo'];
$breadcrumbs = 'Cobranzas / Estudiantes / ' . $estudiante['nombre_completo'];
include __DIR__ . '/../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:1.3fr 1fr;gap:16px;">
    <div class="card">
        <h3>Información general</h3>
        <p><strong>Código:</strong> <?= htmlspecialchars($estudiante['codigo_estudiante']) ?></p>
        <p><strong>Colegio:</strong> <?= htmlspecialchars($estudiante['colegio_nombre'] ?? 'No asignado') ?></p>
        <p><strong>Sede:</strong> <?= htmlspecialchars($estudiante['sede_nombre'] ?? 'No asignada') ?></p>
        <p><strong>Responsable financiero:</strong> <?= htmlspecialchars($estudiante['responsable_nombre'] ?? 'Sin responsable') ?></p>
        <p><strong>Grado:</strong> <?= htmlspecialchars($estudiante['grado']) ?></p>
        <p><strong>Curso:</strong> <?= htmlspecialchars($estudiante['curso']) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($estudiante['estado']) ?></p>
        <div style="margin-top:16px;display:flex;gap:10px;">
            <a class="btn" href="index.php?route=estudiantes/edit&id=<?= $estudiante['id_estudiante'] ?>">Editar</a>
            <form method="post" action="index.php?route=estudiantes/delete" onsubmit="return confirm('¿Eliminar estudiante?');">
                <input type="hidden" name="id" value="<?= $estudiante['id_estudiante'] ?>">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(Core\Helpers::csrfToken()) ?>">
                <button class="btn secondary" type="submit">Eliminar</button>
            </form>
        </div>
    </div>
    <div class="card">
        <h3>Resumen financiero</h3>
        <p><strong>Deudas activas:</strong> <?= count(array_filter($deudas, fn($item) => $item['estado'] !== 'pagado')) ?></p>
        <p><strong>Total saldo:</strong> $ <?= number_format(array_sum(array_column($deudas, 'saldo_actual')), 0, ',', '.') ?></p>
        <p><strong>Pagos registrados:</strong> <?= count($pagos) ?></p>
    </div>
</div>
<div class="card" style="margin-top:16px;">
    <h3>Deudas del estudiante</h3>
    <table class="table">
        <thead><tr><th>Concepto</th><th>Periodo</th><th>Valor inicial</th><th>Saldo</th><th>Estado</th><th>Vencimiento</th></tr></thead>
        <tbody>
            <?php foreach ($deudas as $deuda): ?>
                <tr>
                    <td><?= htmlspecialchars($deuda['concepto_nombre'] ?? 'Sin concepto') ?></td>
                    <td><?= htmlspecialchars($deuda['periodo_nombre'] ?? 'N/A') ?></td>
                    <td>$ <?= number_format($deuda['valor_inicial'], 0, ',', '.') ?></td>
                    <td>$ <?= number_format($deuda['saldo_actual'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($deuda['estado']) ?></td>
                    <td><?= htmlspecialchars($deuda['fecha_vencimiento'] ?? 'Sin definir') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($deudas)): ?>
                <tr><td colspan="6">No hay deudas registradas para este estudiante.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="card" style="margin-top:16px;">
    <h3>Pagos</h3>
    <table class="table">
        <thead><tr><th>Fecha</th><th>Valor</th><th>Método</th><th>Referencia</th></tr></thead>
        <tbody>
            <?php foreach ($pagos as $pago): ?>
                <tr>
                    <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                    <td>$ <?= number_format($pago['valor_total'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                    <td><?= htmlspecialchars($pago['referencia']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($pagos)): ?>
                <tr><td colspan="4">No hay pagos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
