<?php
$title = 'Limpieza de cobranzas';
$pageTitle = 'Limpieza de datos de cobranza';
$breadcrumbs = 'Administración / Limpieza de cobranzas';
$resumen = $resumen ?? [];
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card" style="max-width:980px;margin:0 auto;">
    <h3 style="margin-top:0;">Solo para administradores globales</h3>
    <p class="small" style="margin-top:4px;">Esta acción elimina estudiantes, responsables, deudas, pagos y acuerdos. No modifica usuarios ni parametrización. Antes de borrar se descarga automáticamente un respaldo SQL.</p>

    <div class="alert error" role="alert" style="margin:12px 0;">
        <strong>Advertencia:</strong> La operación es irreversible. Descarga y conserva el archivo SQL que se generará antes de proceder.
    </div>

    <h4>Resumen actual</h4>
    <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;">
        <?php foreach ($resumen as $tabla => $total): ?>
            <div class="stat">
                <div class="stat-label"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $tabla))) ?></div>
                <div class="stat-value"><?= number_format((int) $total, 0, ',', '.') ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" action="index.php?route=mantenimiento/ejecutar-limpieza" data-confirm="Se generará un respaldo SQL y luego se eliminarán los datos de cobranza. ¿Deseas continuar?" style="margin-top:16px;display:flex;flex-direction:column;gap:10px;">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <label style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="confirmacion" required>
            <span>Entiendo que se eliminará toda la información de cobranza y conservaré el respaldo SQL que se descargará.</span>
        </label>
        <div class="actions" style="justify-content:flex-end;">
            <button type="submit" class="btn danger">Respaldar y eliminar cobranza</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
