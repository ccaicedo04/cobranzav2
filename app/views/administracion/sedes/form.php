<?php
$title = 'Editar sede';
$pageTitle = 'Actualizar sede';
$breadcrumbs = 'Administración / Sedes / Edición';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=sedes/update">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="id_sede" value="<?= $sede['id_sede'] ?>">
        <?php if (($usuario['rol'] ?? '') === 'admin_global'): ?>
            <label>Colegio</label>
            <select name="id_colegio" required>
                <?php foreach ($colegios as $colegio): ?>
                    <option value="<?= $colegio['id_colegio'] ?>" <?= $colegio['id_colegio'] == $sede['id_colegio'] ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <label>Nombre</label>
        <input name="nombre" value="<?= htmlspecialchars($sede['nombre']) ?>" required>
        <label>Dirección</label>
        <input name="direccion" value="<?= htmlspecialchars($sede['direccion']) ?>">
        <label>Teléfono</label>
        <input name="telefono" value="<?= htmlspecialchars($sede['telefono']) ?>">
        <label>Correo</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($sede['correo']) ?>">
        <label>Estado</label>
        <select name="estado">
            <option value="activo" <?= $sede['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $sede['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
            <a class="btn secondary" href="index.php?route=sedes">Cancelar</a>
            <button class="btn" type="submit">Actualizar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
