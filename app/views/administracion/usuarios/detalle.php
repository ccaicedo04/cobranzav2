<?php
$title = 'Detalle usuario';
$pageTitle = 'Detalle de usuario';
$breadcrumbs = 'Administración / Usuarios / Detalle';
$rolesDisponibles = $rolesDisponibles ?? ['agente' => 'Agente'];
include __DIR__ . '/../../_partials/header.php';

$idsColegios = array_map('intval', $colegios ? array_column($colegios, 'id_colegio') : []);
$idsSedes = array_map('intval', $sedes ? array_column($sedes, 'id_sede') : []);
$modulosSeleccionados = is_array($modulos) ? $modulos : [];
$mapModuloNombres = [];
foreach ($modulosDisponibles as $moduloDisponible) {
    $mapModuloNombres[$moduloDisponible['codigo']] = $moduloDisponible['nombre'];
}
?>
<div class="grid" style="grid-template-columns:1.2fr 1fr;gap:18px;">
    <div class="card">
        <h3>Información general</h3>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuarioDetalle['nombre_completo']) ?></p>
        <p><strong>Usuario:</strong> <?= htmlspecialchars($usuarioDetalle['usuario']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($usuarioDetalle['email']) ?></p>
        <p><strong>Rol:</strong> <?= htmlspecialchars(strtoupper($usuarioDetalle['rol'])) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($usuarioDetalle['estado']) ?></p>
        <p><strong>Último colegio base:</strong> <?= htmlspecialchars($usuarioDetalle['colegio_nombre'] ?? 'Sin definir') ?></p>
        <p><strong>Última sede base:</strong> <?= htmlspecialchars($usuarioDetalle['sede_nombre'] ?? 'Sin definir') ?></p>
        <div class="actions" style="margin-top:12px;display:flex;gap:10px;">
            <a class="btn secondary" href="index.php?route=usuarios">Regresar</a>
        </div>
    </div>
    <div class="card">
        <h3>Actualizar usuario</h3>
        <form method="post" action="index.php?route=usuarios/update" data-confirm="¿Deseas guardar los cambios de este usuario?" id="formActualizarUsuario">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="id_usuario" value="<?= (int) $usuarioDetalle['id_usuario'] ?>">
            <label>Nombre completo</label>
            <input name="nombre_completo" value="<?= htmlspecialchars($usuarioDetalle['nombre_completo']) ?>" required>
            <label>Correo</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuarioDetalle['email']) ?>" required>
            <label>Usuario</label>
            <input name="usuario" value="<?= htmlspecialchars($usuarioDetalle['usuario']) ?>" required>
            <label>Nueva contraseña <span class="small">(déjalo vacío si no deseas cambiarla)</span></label>
            <input type="password" name="password" placeholder="Actualizar contraseña opcional">
            <label>Rol</label>
            <select name="rol" id="rolDetalle" onchange="toggleAsignacionDetalle()">
                <?php foreach ($rolesDisponibles as $claveRol => $nombreRol): ?>
                    <option value="<?= htmlspecialchars($claveRol) ?>" <?= $usuarioDetalle['rol'] === $claveRol ? 'selected' : '' ?>><?= htmlspecialchars($nombreRol) ?></option>
                <?php endforeach; ?>
            </select>
            <div id="asignacionDetalle" style="margin-top:12px;">
                <label>Colegios asignados</label>
                <select name="permisos_colegios[]" id="colegiosDetalle" multiple size="4" onchange="filtrarSedesDetalle()">
                    <?php foreach ($opcionesColegios as $colegio): ?>
                        <option value="<?= $colegio['id_colegio'] ?>" <?= in_array((int) $colegio['id_colegio'], $idsColegios, true) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($colegio['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Sedes asignadas</label>
                <select name="permisos_sedes[]" id="sedesDetalle" multiple size="6">
                    <?php foreach ($opcionesSedes as $sede): ?>
                        <option value="<?= $sede['id_sede'] ?>" data-colegio="<?= $sede['id_colegio'] ?>" <?= in_array((int) $sede['id_sede'], $idsSedes, true) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($sede['colegio_nombre'] ?? 'Colegio') . ' - ' . $sede['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <fieldset style="margin-top:12px;">
                <legend>Módulos habilitados</legend>
                <div class="chips">
                    <?php if (!empty($modulosDisponibles)): ?>
                        <?php foreach ($modulosDisponibles as $modulo): ?>
                            <label style="display:block;margin-bottom:6px;">
                                <input type="checkbox" name="permisos_modulos[]" value="<?= htmlspecialchars($modulo['codigo']) ?>" <?= in_array($modulo['codigo'], $modulosSeleccionados, true) ? 'checked' : '' ?>> <?= htmlspecialchars($modulo['nombre']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="small" style="margin:0;">No hay módulos definidos. Configura módulos en parametrización para habilitarlos.</p>
                    <?php endif; ?>
                </div>
            </fieldset>
            <label style="margin-top:12px;">Estado</label>
            <select name="estado">
                <option value="activo" <?= $usuarioDetalle['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $usuarioDetalle['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn" type="submit">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
<div class="grid" style="grid-template-columns:1fr 1fr;gap:18px;margin-top:18px;">
    <div class="card">
        <h3>Colegios asignados</h3>
        <?php if (!empty($colegios)): ?>
            <ul class="small">
                <?php foreach ($colegios as $colegio): ?>
                    <li><strong><?= htmlspecialchars($colegio['nombre']) ?></strong> <?= !empty($colegio['nit']) ? '(NIT ' . htmlspecialchars($colegio['nit']) . ')' : '' ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="small">Sin colegios asignados.</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Sedes asignadas</h3>
        <?php if (!empty($sedes)): ?>
            <ul class="small">
                <?php foreach ($sedes as $sede): ?>
                    <li><strong><?= htmlspecialchars($sede['nombre']) ?></strong> <?= htmlspecialchars($sede['colegio_nombre'] ?? '') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="small">Sin sedes asignadas.</p>
        <?php endif; ?>
    </div>
</div>
<div class="card" style="margin-top:18px;">
    <h3>Resumen de módulos habilitados</h3>
    <?php if (!empty($modulosSeleccionados)): ?>
        <div class="chips" style="display:flex;flex-wrap:wrap;gap:8px;">
            <?php foreach ($modulosSeleccionados as $modulo): ?>
                <span class="badge" style="background:#eef2ff;color:#1d4ed8;">
                    <?= htmlspecialchars($mapModuloNombres[$modulo] ?? $modulo) ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="small">No hay módulos asociados actualmente.</p>
    <?php endif; ?>
</div>
<script>
function toggleAsignacionDetalle() {
    const rol = document.getElementById('rolDetalle').value;
    const contenedor = document.getElementById('asignacionDetalle');
    contenedor.style.display = (rol === 'admin_global') ? 'none' : 'block';
}

function filtrarSedesDetalle() {
    const selectColegio = document.getElementById('colegiosDetalle');
    const selectSedes = document.getElementById('sedesDetalle');
    if (!selectColegio || !selectSedes) return;
    const seleccionados = Array.from(selectColegio.selectedOptions).map(option => option.value);
    Array.from(selectSedes.options).forEach(option => {
        if (!option.value) return;
        const pertenece = option.dataset.colegio;
        option.hidden = seleccionados.length && !seleccionados.includes(pertenece);
    });
}

toggleAsignacionDetalle();
filtrarSedesDetalle();
</script>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
