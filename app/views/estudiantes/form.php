<?php
$title = isset($estudiante) ? 'Editar estudiante' : 'Nuevo estudiante';
$pageTitle = $title;
$breadcrumbs = 'Cobranzas / Estudiantes / ' . $title;
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=estudiantes/<?= isset($estudiante) ? 'update' : 'store' ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <?php if (isset($estudiante)): ?>
            <input type="hidden" name="id_estudiante" value="<?= $estudiante['id_estudiante'] ?>">
        <?php endif; ?>
        <div class="two">
            <div>
                <label>Nombre completo</label>
                <input name="nombre_completo" value="<?= htmlspecialchars($estudiante['nombre_completo'] ?? '') ?>" required>
            </div>
            <div>
                <label>Código</label>
                <input name="codigo_estudiante" value="<?= htmlspecialchars($estudiante['codigo_estudiante'] ?? '') ?>" required>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Grado</label>
                <input name="grado" value="<?= htmlspecialchars($estudiante['grado'] ?? '') ?>" required>
            </div>
            <div>
                <label>Curso</label>
                <input name="curso" value="<?= htmlspecialchars($estudiante['curso'] ?? '') ?>" required>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Responsable financiero</label>
                <select name="id_responsable" required>
                    <?php foreach ($responsables as $responsable): ?>
                        <option value="<?= $responsable['id_responsable'] ?>" <?= (($estudiante['id_responsable'] ?? '') == $responsable['id_responsable']) ? 'selected' : '' ?>><?= htmlspecialchars($responsable['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Estado</label>
                <?php $estado = $estudiante['estado'] ?? 'activo'; ?>
                <select name="estado">
                    <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <a class="btn secondary" href="index.php?route=estudiantes">Cancelar</a>
            <button class="btn" type="submit">Guardar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
