<?php
$title = 'Usuarios';
$pageTitle = 'Usuarios';
$breadcrumbs = 'Administración / Usuarios';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Usuarios del sistema</h3>
        <table class="table">
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Colegios</th><th>Sedes</th><th>Módulos</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($usuarios as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($fila['usuario']) ?></td>
                        <td><?= htmlspecialchars(strtoupper($fila['rol'])) ?></td>
                        <td>
                            <?php if (!empty($fila['permisos_colegios_array'])): ?>
                                <?= htmlspecialchars(implode(', ', array_map(fn($id) => $mapColegios[$id] ?? ('ID ' . $id), $fila['permisos_colegios_array']))) ?>
                            <?php elseif (!empty($fila['colegio_nombre'])): ?>
                                <?= htmlspecialchars($fila['colegio_nombre']) ?>
                            <?php else: ?>
                                <span class="tag">No asignado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($fila['permisos_sedes_array'])): ?>
                                <?= htmlspecialchars(implode(', ', array_map(fn($id) => $mapSedes[$id] ?? ('ID ' . $id), $fila['permisos_sedes_array']))) ?>
                            <?php elseif (!empty($fila['sede_nombre'])): ?>
                                <?= htmlspecialchars($fila['sede_nombre']) ?>
                            <?php else: ?>
                                <span class="tag">No asignada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($fila['permisos_modulos_array'])): ?>
                                <?= htmlspecialchars(implode(', ', array_map('ucfirst', $fila['permisos_modulos_array']))) ?>
                            <?php else: ?>
                                <span class="tag">Sin módulos</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($fila['estado']) ?></td>
                        <td><a class="btn secondary" href="index.php?route=usuarios/detalle&id=<?= $fila['id_usuario'] ?>">Detalle</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="8">No hay usuarios registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Crear usuario</h3>
        <form method="post" action="index.php?route=usuarios/store">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <label>Nombre completo</label>
            <input name="nombre_completo" required>
            <label>Correo</label>
            <input type="email" name="email" required>
            <label>Usuario</label>
            <input name="usuario" required>
            <label>Contraseña</label>
            <input type="password" name="password" required>
            <label>Rol</label>
            <select name="rol" id="rolSelector" onchange="toggleAsignacion()">
                <option value="admin_global">Administrador Global</option>
                <option value="admin_colegio">Administrador Colegio</option>
                <option value="agente" selected>Agente</option>
            </select>
            <div id="asignacionColegio" style="margin-top:12px;">
                <label>Colegios asignados</label>
                <select name="permisos_colegios[]" id="colegioSelector" onchange="filtrarSedes()" multiple size="4">
                    <?php foreach ($colegios as $colegio): ?>
                        <option value="<?= $colegio['id_colegio'] ?>"><?= htmlspecialchars($colegio['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Sedes asignadas</label>
                <select name="permisos_sedes[]" id="sedeSelector" multiple size="6">
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id_sede'] ?>" data-colegio="<?= $sede['id_colegio'] ?>"><?= htmlspecialchars($sede['colegio_nombre'] . ' - ' . $sede['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <fieldset style="margin-top:12px;">
                <legend>Permisos por módulo</legend>
                <div class="chips">
                    <?php foreach ($modulos as $modulo): ?>
                        <label style="display:block;margin-bottom:6px;">
                            <input type="checkbox" name="permisos_modulos[]" value="<?= $modulo ?>" checked> <?= ucfirst($modulo) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <label style="margin-top:12px;">Estado</label>
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
<script>
function toggleAsignacion() {
    const rol = document.getElementById('rolSelector').value;
    const asignacion = document.getElementById('asignacionColegio');
    asignacion.style.display = (rol === 'admin_global') ? 'none' : 'block';
}

function filtrarSedes() {
    const colegio = document.getElementById('colegioSelector');
    const sedeSelector = document.getElementById('sedeSelector');
    if (!colegio || !sedeSelector) return;
    const seleccionados = [...colegio.selectedOptions].map(opt => opt.value);
    [...sedeSelector.options].forEach(option => {
        if (!option.value) return;
        const pertenece = option.dataset.colegio;
        option.hidden = seleccionados.length && !seleccionados.includes(pertenece);
    });
}

toggleAsignacion();
filtrarSedes();
</script>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
