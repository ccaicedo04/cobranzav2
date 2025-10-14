<?php
$title = 'Colegios';
$pageTitle = 'Colegios';
$breadcrumbs = 'Administración / Colegios';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Listado de colegios</h3>
        <table class="table">
            <thead><tr><th>Nombre</th><th>NIT</th><th>Teléfono</th><th>Correo</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($colegios as $colegio): ?>
                    <tr>
                        <td><?= htmlspecialchars($colegio['nombre']) ?></td>
                        <td><?= htmlspecialchars($colegio['nit']) ?></td>
                        <td><?= htmlspecialchars($colegio['telefono']) ?></td>
                        <td><?= htmlspecialchars($colegio['correo']) ?></td>
                        <td><?= htmlspecialchars($colegio['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($colegios)): ?>
                    <tr><td colspan="5">No hay colegios registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Nuevo colegio</h3>
        <form method="post" action="index.php?route=colegios/store">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <label>Nombre</label>
            <input name="nombre" required>
            <label>NIT</label>
            <input name="nit" required>
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
