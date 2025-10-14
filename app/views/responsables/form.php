<?php
$title = isset($responsable) ? 'Editar responsable' : 'Nuevo responsable';
$pageTitle = $title;
$breadcrumbs = 'Cobranzas / Responsables / ' . $title;
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=responsables/<?= isset($responsable) ? 'update' : 'store' ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <?php if (isset($responsable)): ?>
            <input type="hidden" name="id_responsable" value="<?= $responsable['id_responsable'] ?>">
        <?php endif; ?>
        <div class="two">
            <div>
                <label>Nombre completo</label>
                <input name="nombre_completo" value="<?= htmlspecialchars($responsable['nombre_completo'] ?? '') ?>" required>
            </div>
            <div>
                <label>Tipo de documento</label>
                <select name="tipo_documento" required>
                    <?php $tipo = $responsable['tipo_documento'] ?? ''; ?>
                    <option value="CC" <?= $tipo === 'CC' ? 'selected' : '' ?>>Cédula</option>
                    <option value="TI" <?= $tipo === 'TI' ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                    <option value="CE" <?= $tipo === 'CE' ? 'selected' : '' ?>>Cédula de extranjería</option>
                </select>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Número de documento</label>
                <input name="numero_documento" value="<?= htmlspecialchars($responsable['numero_documento'] ?? '') ?>" required>
            </div>
            <div>
                <label>Teléfono</label>
                <input name="telefono" value="<?= htmlspecialchars($responsable['telefono'] ?? '') ?>" required>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Correo electrónico</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($responsable['correo'] ?? '') ?>" required>
            </div>
            <div>
                <label>Dirección</label>
                <input name="direccion" value="<?= htmlspecialchars($responsable['direccion'] ?? '') ?>">
            </div>
        </div>
        <div class="two">
            <div>
                <label>Sede</label>
                <select name="id_sede">
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id_sede'] ?>" <?= (($responsable['id_sede'] ?? '') == $sede['id_sede']) ? 'selected' : '' ?>><?= htmlspecialchars($sede['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Estado</label>
                <?php $estado = $responsable['estado'] ?? 'activo'; ?>
                <select name="estado">
                    <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <a class="btn secondary" href="index.php?route=responsables">Cancelar</a>
            <button class="btn" type="submit">Guardar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
