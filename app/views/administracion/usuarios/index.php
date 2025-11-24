<?php
$title = 'Usuarios';
$pageTitle = 'Usuarios';
$breadcrumbs = 'Administración / Usuarios';
$rolesDisponibles = $rolesDisponibles ?? ['agente' => 'Agente'];
$modulosPorDefecto = $modulosPorDefecto ?? [];
$formDisabled = false;
$mostrarAdvertenciaContexto = empty($colegios) || empty($sedes);
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;align-items:start;gap:20px;">
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
                                <?= htmlspecialchars(implode(', ', array_map(function ($codigo) use ($mapModulos) {
                                    return $mapModulos[$codigo] ?? ucfirst($codigo);
                                }, $fila['permisos_modulos_array']))) ?>
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
    <div class="card" style="position:sticky;top:10px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
            <div>
                <h3 style="margin:0;">Crear usuario</h3>
                <p class="small" style="margin:4px 0 0;">Completa los datos básicos, asigna colegios/sedes y el módulo que podrá usar.</p>
            </div>
            <span class="tag" style="align-self:center;">Nuevo</span>
        </div>
        <?php if ($mostrarAdvertenciaContexto): ?>
            <div class="alert error" style="margin-bottom:14px;">
                ⚠️ Para completar el formulario, primero configura al menos un colegio, una sede y los módulos en Parametrización.
            </div>
            <ul class="small" style="margin:-4px 0 10px 16px; color:#6b7280;">
                <li>Configura <strong>Colegios</strong> y <strong>Sedes</strong> desde Parametrización.</li>
                <li>Activa los <strong>Módulos</strong> que podrá usar el nuevo usuario.</li>
                <li>Vuelve aquí y selecciona las opciones para habilitar el guardado.</li>
            </ul>
        <?php endif; ?>
        <form method="post" action="index.php?route=usuarios/store" data-confirm="¿Deseas crear el nuevo usuario con los permisos seleccionados?" style="display:flex;flex-direction:column;gap:10px;">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <h4 style="margin:6px 0 0;">Datos básicos</h4>
            <label>Nombre completo</label>
            <input name="nombre_completo" placeholder="Ej: Laura Gómez" <?= $formDisabled ? 'disabled' : '' ?> required>
            <label>Correo</label>
            <input type="email" name="email" placeholder="correo@colegio.edu" <?= $formDisabled ? 'disabled' : '' ?> required>
            <label>Usuario</label>
            <input name="usuario" placeholder="lgomez" <?= $formDisabled ? 'disabled' : '' ?> required>
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="Mínimo 6 caracteres" <?= $formDisabled ? 'disabled' : '' ?> required>
            <label>Rol</label>
            <select name="rol" id="rolSelector" onchange="toggleAsignacion()" <?= $formDisabled ? 'disabled' : '' ?> >
                <?php foreach ($rolesDisponibles as $claveRol => $nombreRol): ?>
                    <option value="<?= htmlspecialchars($claveRol) ?>" <?= $claveRol === 'agente' ? 'selected' : '' ?>><?= htmlspecialchars($nombreRol) ?></option>
                <?php endforeach; ?>
            </select>
            <h4 style="margin:10px 0 0;">Asignaciones</h4>
            <div id="asignacionColegio" style="display:flex;flex-direction:column;gap:8px;">
                <label>Colegios asignados</label>
                <?php if (!empty($colegios)): ?>
                    <select name="permisos_colegios[]" id="colegioSelector" onchange="filtrarSedes()" multiple size="4" <?= $formDisabled ? 'disabled' : '' ?> >
                        <?php foreach ($colegios as $index => $colegio): ?>
                            <option value="<?= $colegio['id_colegio'] ?>" <?= $index === 0 ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="small" style="margin:4px 0 0;">No hay colegios disponibles.</p>
                <?php endif; ?>
                <label>Sedes asignadas</label>
                <?php if (!empty($sedes)): ?>
                    <select name="permisos_sedes[]" id="sedeSelector" multiple size="6" <?= $formDisabled ? 'disabled' : '' ?> >
                        <?php foreach ($sedes as $index => $sede): ?>
                            <option value="<?= $sede['id_sede'] ?>" data-colegio="<?= $sede['id_colegio'] ?>" <?= $index === 0 ? 'selected' : '' ?>><?= htmlspecialchars($sede['colegio_nombre'] . ' - ' . $sede['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="small" style="margin:4px 0 0;">No hay sedes disponibles.</p>
                <?php endif; ?>
            </div>
            <fieldset style="margin-top:8px;">
                <legend>Permisos por módulo</legend>
                <div class="chips" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:6px;">
                    <?php if (!empty($modulos)): ?>
                        <?php foreach ($modulos as $modulo): ?>
                            <label style="display:flex;align-items:center;gap:8px;padding:6px 8px;border:1px solid var(--border);border-radius:10px;">
                                <input type="checkbox" name="permisos_modulos[]" value="<?= htmlspecialchars($modulo['codigo']) ?>" <?= in_array($modulo['codigo'], $modulosPorDefecto, true) ? 'checked' : '' ?> <?= $formDisabled ? 'disabled' : '' ?> >
                                <?= htmlspecialchars($modulo['nombre']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <input type="hidden" name="permisos_modulos[]" value="cobranzas">
                        <p class="small" style="margin:0;">No hay módulos configurados aún. Se asignará <strong>Cobranzas</strong> por defecto hasta que actives más opciones en Parametrización.</p>
                    <?php endif; ?>
                </div>
            </fieldset>
            <label style="margin-top:4px;">Estado</label>
            <select name="estado" <?= $formDisabled ? 'disabled' : '' ?> >
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn primary" type="submit" <?= $formDisabled ? 'disabled' : '' ?>>Crear usuario</button>
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

function autoSeleccionInicial() {
    const colegioSelector = document.getElementById('colegioSelector');
    const sedeSelector = document.getElementById('sedeSelector');
    if (colegioSelector && colegioSelector.options.length > 0 && colegioSelector.selectedOptions.length === 0) {
        colegioSelector.options[0].selected = true;
    }
    if (sedeSelector && sedeSelector.options.length > 0 && sedeSelector.selectedOptions.length === 0) {
        sedeSelector.options[0].selected = true;
    }
}

toggleAsignacion();
autoSeleccionInicial();
filtrarSedes();
</script>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
