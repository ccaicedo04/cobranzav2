<?php
$title = 'Sedes';
$pageTitle = 'Sedes';
$breadcrumbs = 'Administración / Sedes';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <h3 style="margin:0;">Listado de sedes</h3>
            <?php if (!empty($colegios)): ?>
                <form method="get" action="index.php" style="display:flex;gap:8px;align-items:center;">
                    <input type="hidden" name="route" value="sedes">
                    <select name="id_colegio" onchange="this.form.submit()">
                        <option value="">Todos los colegios</option>
                        <?php foreach ($colegios as $colegio): ?>
                            <option value="<?= $colegio['id_colegio'] ?>" <?= (($_GET['id_colegio'] ?? '') == $colegio['id_colegio']) ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
        </div>
        <table class="table">
            <thead><tr><th>Colegio</th><th>Sede</th><th>Contacto</th><th>Estado</th><th style="width:140px;">Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($sedes as $sede): ?>
                    <tr>
                        <td><?= htmlspecialchars($sede['colegio_nombre']) ?></td>
                        <td>
                            <div><strong><?= htmlspecialchars($sede['nombre']) ?></strong></div>
                            <small><?= htmlspecialchars($sede['direccion']) ?></small>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($sede['telefono'] ?: 'Sin teléfono') ?></div>
                            <small><?= htmlspecialchars($sede['correo'] ?: 'Sin correo') ?></small>
                        </td>
                        <td><span class="badge" style="background:<?= $sede['estado'] === 'activo' ? '#dcfce7' : '#fee2e2' ?>;color:<?= $sede['estado'] === 'activo' ? '#166534' : '#991b1b' ?>;">
                            <?= strtoupper($sede['estado']) ?></span></td>
                        <td style="display:flex;gap:6px;">
                            <a class="btn secondary" href="index.php?route=sedes/detalle&id=<?= $sede['id_sede'] ?>">Detalle</a>
                            <a class="btn" href="index.php?route=sedes/edit&id=<?= $sede['id_sede'] ?>">Editar</a>
                        </td>
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
        <form method="post" action="index.php?route=sedes/store" data-confirm="¿Deseas registrar la nueva sede institucional?">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <?php if (($usuario['rol'] ?? '') === 'admin_global'): ?>
                <label>Colegio</label>
                <select name="id_colegio" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($colegios as $colegio): ?>
                        <option value="<?= $colegio['id_colegio'] ?>"><?= htmlspecialchars($colegio['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
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
