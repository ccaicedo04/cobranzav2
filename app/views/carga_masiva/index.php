<?php
$title = 'Carga masiva';
$pageTitle = 'Carga masiva de datos';
$breadcrumbs = 'Cobranzas / Carga masiva';
include __DIR__ . '/../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Historial de cargas</h3>
        <table class="table">
            <thead><tr><th>Fecha</th><th>Archivo</th><th>Resultado</th><th>Mensaje</th></tr></thead>
            <tbody>
                <?php foreach ($cargas as $carga): ?>
                    <tr>
                        <td><?= htmlspecialchars($carga['fecha_registro'] ?? '') ?></td>
                        <td><?= htmlspecialchars($carga['archivo_original']) ?></td>
                        <td><?= htmlspecialchars($carga['resultado']) ?></td>
                        <td><?= htmlspecialchars($carga['mensaje']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($cargas)): ?>
                    <tr><td colspan="4">Aún no se han realizado cargas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Subir archivo</h3>
        <form method="post" action="index.php?route=carga-masiva/store" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <label>Archivo Excel (.xlsx)</label>
            <input type="file" name="archivo" accept=".xlsx">
            <p class="small">El archivo será validado y cargado en segundo plano.</p>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn" type="submit">Subir</button>
            </div>
        </form>
        <p style="margin-top:14px;">Descarga la plantilla oficial:</p>
        <a class="btn secondary" href="plantillas/plantilla_carga_masiva.php">Descargar plantilla</a>
    </div>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
