<?php
$title = 'Usuarios';
$pageTitle = 'Usuarios';
$breadcrumbs = 'Administración / Usuarios';
$usuarios = $usuarios ?? [];
$colegios = $colegios ?? [];
$sedes = $sedes ?? [];
$modulos = $modulos ?? [];
$rolesDisponibles = $rolesDisponibles ?? ['agente' => 'Agente'];
$modulosPorDefecto = $modulosPorDefecto ?? [];
$mapColegios = $mapColegios ?? [];
$mapSedes = $mapSedes ?? [];
$mapModulos = $mapModulos ?? [];
$filtros = $filtros ?? ['busqueda' => '', 'estado' => '', 'rol' => ''];
$totalUsuarios = count($usuarios);
$formDisabled = false;
$mostrarAdvertenciaContexto = empty($colegios) || empty($sedes);
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card" id="form-usuario" style="margin-bottom:18px;">
    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-start;gap:14px;">
        <div style="max-width:780px;">
            <h3 style="margin:0;">Administrar usuarios</h3>
            <p class="small" style="margin:4px 0 8px;">Primero crea o edita un usuario con su contexto y módulos; debajo verás el listado actualizado. Todos los campos con * son obligatorios.</p>
            <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                <span class="tag">Formulario</span>
                <span class="tag" style="background:#eef2ff;color:#312e81;">Usuarios registrados: <?= $totalUsuarios ?></span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <button class="btn primary" form="formCrearUsuario" type="submit">Guardar usuario</button>
        </div>
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
        <div class="alert" style="background:#f8fafc;border:1px dashed #cbd5e1;color:#0f172a;">
            Aún puedes crear el usuario: se guardará sin colegio ni sede hasta que los definas. Luego podrás editarlo y
            asignar el contexto desde esta misma pantalla.
        </div>
    <?php endif; ?>
    <form id="formCrearUsuario" method="post" action="index.php?route=usuarios/store" data-confirm="¿Deseas crear el nuevo usuario con los permisos seleccionados?" style="display:flex;flex-direction:column;gap:14px;" data-form-usuario onsubmit="return validarUsuario();">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <h4 style="margin:6px 0 0;">Datos básicos</h4>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;align-items:end;">
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Nombre completo *</label>
                <input name="nombre_completo" placeholder="Ej: Laura Gómez" <?= $formDisabled ? 'disabled' : '' ?> required>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Correo *</label>
                <input type="email" name="email" placeholder="correo@colegio.edu" <?= $formDisabled ? 'disabled' : '' ?> required>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Usuario *</label>
                <input name="usuario" placeholder="lgomez" <?= $formDisabled ? 'disabled' : '' ?> required>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Rol</label>
                <select name="rol" id="rolSelector" onchange="toggleAsignacion()" <?= $formDisabled ? 'disabled' : '' ?> >
                    <?php foreach ($rolesDisponibles as $claveRol => $nombreRol): ?>
                        <option value="<?= htmlspecialchars($claveRol) ?>" <?= $claveRol === 'agente' ? 'selected' : '' ?>><?= htmlspecialchars($nombreRol) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;align-items:end;">
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Contraseña *</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres" <?= $formDisabled ? 'disabled' : '' ?>>
                <small class="small" style="color:#6b7280;">Si la dejas vacía se asignará 123456 automáticamente.</small>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Confirmar contraseña *</label>
                <input type="password" name="password_confirm" placeholder="Repite la contraseña" <?= $formDisabled ? 'disabled' : '' ?>>
                <small class="small" style="color:#6b7280;">Debe coincidir con la contraseña o se completará con 123456.</small>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Estado</label>
                <select name="estado" <?= $formDisabled ? 'disabled' : '' ?> >
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
        <div class="card" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:12px 14px;">
            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
                <h4 style="margin:0;">Asignaciones</h4>
                <small class="small" style="color:#475569;">Define el alcance del usuario antes de guardarlo.</small>
            </div>
            <div id="asignacionColegio" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;margin-top:10px;">
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label>Colegios asignados</label>
                    <?php if (!empty($colegios)): ?>
                        <select name="permisos_colegios[]" id="colegioSelector" onchange="filtrarSedes()" multiple size="5" <?= $formDisabled ? 'disabled' : '' ?> >
                            <?php foreach ($colegios as $index => $colegio): ?>
                                <option value="<?= $colegio['id_colegio'] ?>" <?= $index === 0 ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p class="small" style="margin:4px 0 0;">No hay colegios disponibles.</p>
                    <?php endif; ?>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label>Sedes asignadas</label>
                    <?php if (!empty($sedes)): ?>
                        <select name="permisos_sedes[]" id="sedeSelector" multiple size="7" <?= $formDisabled ? 'disabled' : '' ?> >
                            <?php foreach ($sedes as $index => $sede): ?>
                                <option value="<?= $sede['id_sede'] ?>" data-colegio="<?= $sede['id_colegio'] ?>" <?= $index === 0 ? 'selected' : '' ?>><?= htmlspecialchars($sede['colegio_nombre'] . ' - ' . $sede['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p class="small" style="margin:4px 0 0;">No hay sedes disponibles.</p>
                    <?php endif; ?>
                </div>
            </div>
            <fieldset style="margin-top:14px;">
                <legend style="font-weight:600;">Permisos por módulo</legend>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
                    <small class="small" style="color:#475569;">Activa los módulos a los que tendrá acceso.</small>
                    <?php if (!empty($modulos)): ?>
                        <button type="button" class="btn secondary" style="padding:4px 10px;font-size:12px;" onclick="seleccionarTodosModulos(true)">Seleccionar todos</button>
                        <button type="button" class="btn ghost" style="padding:4px 10px;font-size:12px;" onclick="seleccionarTodosModulos(false)">Limpiar</button>
                    <?php endif; ?>
                </div>
                <div class="chips" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;">
                    <?php if (!empty($modulos)): ?>
                        <?php foreach ($modulos as $modulo): ?>
                            <label style="display:flex;align-items:center;gap:8px;padding:10px;border:1px solid var(--border);border-radius:10px;">
                                <input type="checkbox" class="modulo-checkbox" name="permisos_modulos[]" value="<?= htmlspecialchars($modulo['codigo']) ?>" <?= in_array($modulo['codigo'], $modulosPorDefecto, true) ? 'checked' : '' ?> <?= $formDisabled ? 'disabled' : '' ?> >
                                <span style="font-weight:600;"><?= htmlspecialchars($modulo['nombre']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <input type="hidden" name="permisos_modulos[]" value="cobranzas">
                        <p class="small" style="margin:0;">No hay módulos configurados aún. Se asignará <strong>Cobranzas</strong> por defecto hasta que actives más opciones en Parametrización.</p>
                    <?php endif; ?>
                </div>
            </fieldset>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:6px;">
            <button class="btn primary" type="submit" <?= $formDisabled ? 'disabled' : '' ?>>Guardar usuario</button>
        </div>
    </form>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Usuarios del sistema</h3>
            <p class="small" style="margin:4px 0 0;">Consulta los usuarios creados, sus asignaciones y módulos. Total: <?= $totalUsuarios ?></p>
        </div>
        <a class="btn secondary" href="#form-usuario">Ir a crear</a>
    </div>
    <form class="toolbar" method="get" action="index.php" style="margin-top:12px;">
        <input type="hidden" name="route" value="usuarios">
        <input name="busqueda" style="max-width:240px" placeholder="Buscar por nombre, usuario o correo" value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
        <select name="rol">
            <option value="">Todos los roles</option>
            <option value="admin_global" <?= (($filtros['rol'] ?? '') === 'admin_global') ? 'selected' : '' ?>>Admin global</option>
            <option value="admin_colegio" <?= (($filtros['rol'] ?? '') === 'admin_colegio') ? 'selected' : '' ?>>Admin colegio</option>
            <option value="agente" <?= (($filtros['rol'] ?? '') === 'agente') ? 'selected' : '' ?>>Agente</option>
        </select>
        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="activo" <?= (($filtros['estado'] ?? '') === 'activo') ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= (($filtros['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <button class="btn secondary" type="submit">Filtrar</button>
    </form>
    <table class="table" style="margin-top:10px;">
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
<script>
function toggleAsignacion() {
    const rol = document.getElementById('rolSelector').value;
    const asignacion = document.getElementById('asignacionColegio');
    asignacion.style.display = (rol === 'admin_global') ? 'none' : 'grid';
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

function seleccionarTodosModulos(checkAll) {
    document.querySelectorAll('.modulo-checkbox').forEach((input) => {
        if (input.disabled) return;
        input.checked = !!checkAll;
    });
}

function validarUsuario() {
    const form = document.querySelector('[data-form-usuario]');
    if (!form) return true;
    const pass = form.querySelector('input[name="password"]');
    const confirm = form.querySelector('input[name="password_confirm"]');
    if (!pass || !confirm) return true;
    const passValue = pass.value.trim();
    const confirmValue = confirm.value.trim();

    if (passValue === '' && confirmValue === '') {
        pass.value = '123456';
        confirm.value = '123456';
        return true;
    }

    if ((passValue === '' && confirmValue !== '') || (passValue !== '' && confirmValue === '')) {
        alert('Completa ambos campos de contraseña.');
        return false;
    }

    if (passValue.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres.');
        pass.focus();
        return false;
    }

    if (passValue !== confirmValue) {
        alert('Las contraseñas no coinciden.');
        confirm.focus();
        return false;
    }
    return true;
}
</script>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
