<?php
$title = 'Parámetros';
$pageTitle = 'Parámetros del sistema';
$breadcrumbs = 'Administración / Parámetros';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Parámetros configurados</h3>
        <table class="table">
            <thead><tr><th>Clave</th><th>Valor</th><th>Descripción</th></tr></thead>
            <tbody>
                <?php foreach ($parametros as $parametro): ?>
                    <tr>
                        <td><?= htmlspecialchars($parametro['clave']) ?></td>
                        <td><?= htmlspecialchars($parametro['valor']) ?></td>
                        <td><?= htmlspecialchars($parametro['descripcion']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($parametros)): ?>
                    <tr><td colspan="3">No hay parámetros registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Crear parámetro</h3>
        <form method="post" action="index.php?route=parametros/store">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <label>Clave</label>
            <input name="clave" required>
            <label>Valor</label>
            <input name="valor" required>
            <label>Descripción</label>
            <textarea name="descripcion" rows="3"></textarea>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
