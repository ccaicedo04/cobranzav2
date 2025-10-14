<?php
$title = 'Editar concepto';
$pageTitle = 'Actualizar concepto';
$breadcrumbs = 'Parametrización / Conceptos / Edición';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=conceptos/update" data-confirm="¿Guardar cambios del concepto seleccionado?">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="id_concepto" value="<?= $concepto['id_concepto'] ?>">
        <?php if (($usuario['rol'] ?? '') === 'admin_global'): ?>
            <label>Colegio</label>
            <select name="id_colegio" required>
                <?php foreach ($colegios as $colegio): ?>
                    <option value="<?= $colegio['id_colegio'] ?>" <?= $colegio['id_colegio'] == $concepto['id_colegio'] ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <label>Nombre</label>
        <input name="nombre" value="<?= htmlspecialchars($concepto['nombre']) ?>" required>
        <label>Descripción</label>
        <textarea name="descripcion" rows="3"><?= htmlspecialchars($concepto['descripcion']) ?></textarea>
        <label>Tipo</label>
        <select name="tipo">
            <option value="recurrente" <?= $concepto['tipo'] === 'recurrente' ? 'selected' : '' ?>>Recurrente</option>
            <option value="único" <?= $concepto['tipo'] === 'único' ? 'selected' : '' ?>>Único</option>
            <option value="servicio" <?= $concepto['tipo'] === 'servicio' ? 'selected' : '' ?>>Servicio</option>
        </select>
        <label>Valor base</label>
        <input type="number" name="valor_base" value="<?= htmlspecialchars($concepto['valor_base']) ?>" min="0" step="1000" required>
        <label>Estado</label>
        <select name="estado">
            <option value="activo" <?= $concepto['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $concepto['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
            <a class="btn secondary" href="index.php?route=conceptos">Cancelar</a>
            <button class="btn" type="submit">Actualizar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
