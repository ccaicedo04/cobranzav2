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
            <thead><tr><th>Nombre</th><th>NIT</th><th>Contacto</th><th>Estado</th><th style="width:160px;">Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($colegios as $colegio): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($colegio['nombre']) ?></strong><br><small><?= htmlspecialchars($colegio['direccion'] ?: 'Sin dirección') ?></small></td>
                        <td><?= htmlspecialchars($colegio['nit']) ?></td>
                        <td>
                            <div><?= htmlspecialchars($colegio['telefono'] ?: 'Sin teléfono') ?></div>
                            <small><?= htmlspecialchars($colegio['correo'] ?: 'Sin correo') ?></small>
                        </td>
                        <td><span class="badge" style="background:<?= $colegio['estado'] === 'activo' ? '#dcfce7' : '#fee2e2' ?>;color:<?= $colegio['estado'] === 'activo' ? '#166534' : '#991b1b' ?>;"><?= strtoupper($colegio['estado']) ?></span></td>
                        <td style="display:flex;gap:6px;">
                            <a class="btn secondary" href="index.php?route=colegios/detalle&id=<?= $colegio['id_colegio'] ?>">Detalle</a>
                            <a class="btn" href="index.php?route=colegios/edit&id=<?= $colegio['id_colegio'] ?>">Editar</a>
                        </td>
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
        <form method="post" action="index.php?route=colegios/store" data-confirm="¿Deseas registrar el nuevo colegio con los datos proporcionados?">
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
