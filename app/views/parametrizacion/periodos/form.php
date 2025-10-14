<?php
$title = 'Editar período';
$pageTitle = 'Actualizar período';
$breadcrumbs = 'Parametrización / Períodos / Edición';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=periodos/update">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="id_periodo" value="<?= $periodo['id_periodo'] ?>">
        <?php if (($usuario['rol'] ?? '') === 'admin_global'): ?>
            <label>Colegio</label>
            <select name="id_colegio" required>
                <?php foreach ($colegios as $colegio): ?>
                    <option value="<?= $colegio['id_colegio'] ?>" <?= $colegio['id_colegio'] == $periodo['id_colegio'] ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <label>Nombre</label>
        <input name="nombre" value="<?= htmlspecialchars($periodo['nombre']) ?>" required>
        <label>Fecha inicio</label>
        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($periodo['fecha_inicio']) ?>" required>
        <label>Fecha fin</label>
        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($periodo['fecha_fin']) ?>" required>
        <label>Estado</label>
        <select name="estado">
            <option value="activo" <?= $periodo['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $periodo['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
            <a class="btn secondary" href="index.php?route=periodos">Cancelar</a>
            <button class="btn" type="submit">Actualizar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
