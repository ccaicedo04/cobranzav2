<?php
$title = 'Sedes';
$pageTitle = 'Sedes';
$breadcrumbs = 'Administración / Sedes';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Listado de sedes</h3>
        <table class="table">
            <thead><tr><th>Nombre</th><th>Dirección</th><th>Teléfono</th><th>Correo</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($sedes as $sede): ?>
                    <tr>
                        <td><?= htmlspecialchars($sede['nombre']) ?></td>
                        <td><?= htmlspecialchars($sede['direccion']) ?></td>
                        <td><?= htmlspecialchars($sede['telefono']) ?></td>
                        <td><?= htmlspecialchars($sede['correo']) ?></td>
                        <td><?= htmlspecialchars($sede['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($sedes)): ?>
                    <tr><td colspan="5">No hay sedes registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Nueva sede</h3>
        <form method="post" action="index.php?route=sedes/store">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <label>Nombre</label>
            <input name="nombre" required>
            <label>Dirección</label>
            <input name="direccion">
            <label>Teléfono</label>
            <input name="telefono">
            <label>Correo</label>
            <input type="email" name="correo">
            <label>Estado</label>
            <select name="estado">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
