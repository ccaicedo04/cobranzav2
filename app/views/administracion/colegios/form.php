<?php
$title = 'Editar colegio';
$pageTitle = 'Actualizar colegio';
$breadcrumbs = 'Administración / Colegios / Edición';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=colegios/update" data-confirm="¿Confirmas la actualización de la información institucional?">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="id_colegio" value="<?= (int) ($colegio['id_colegio'] ?? 0) ?>">
        <label>Nombre</label>
        <input name="nombre" value="<?= htmlspecialchars($colegio['nombre'] ?? '') ?>" required>
        <label>NIT</label>
        <input name="nit" value="<?= htmlspecialchars($colegio['nit'] ?? '') ?>" required>
        <label>Dirección</label>
        <input name="direccion" value="<?= htmlspecialchars($colegio['direccion'] ?? '') ?>">
        <label>Teléfono</label>
        <input name="telefono" value="<?= htmlspecialchars($colegio['telefono'] ?? '') ?>">
        <label>Correo</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($colegio['correo'] ?? '') ?>">
        <label>Estado</label>
        <?php $estado = $colegio['estado'] ?? 'activo'; ?>
        <select name="estado">
            <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
            <a class="btn secondary" href="index.php?route=colegios">Cancelar</a>
            <button class="btn" type="submit">Actualizar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
