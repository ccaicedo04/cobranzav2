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
$modulosDefaultVista = [
    ['codigo' => 'cobranzas', 'nombre' => 'Cobranzas'],
    ['codigo' => 'administracion', 'nombre' => 'Administración'],
    ['codigo' => 'parametrizacion', 'nombre' => 'Parametrización'],
];
$modulosParaForm = !empty($modulos) ? $modulos : $modulosDefaultVista;
if (empty($modulosPorDefecto) && !empty($modulosParaForm)) {
    $modulosPorDefecto = array_column($modulosParaForm, 'codigo');
}
$totalUsuarios = count($usuarios);
include __DIR__ . '/../../_partials/header.php';
?>

<div class="card" style="margin-bottom:16px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Usuarios</h3>
            <p class="small" style="margin:2px 0 0;">Gestiona los usuarios del sistema y accede a su detalle para editar.</p>
        </div>
        <a class="btn" href="#crear-usuario">Nuevo</a>
    </div>
    <form class="toolbar" method="get" action="index.php" style="margin-top:10px;">
        <input type="hidden" name="route" value="usuarios">
        <input name="busqueda" style="max-width:260px" placeholder="Buscar por nombre, usuario o correo" value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="activo" <?= (($filtros['estado'] ?? '') === 'activo') ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= (($filtros['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <select name="rol">
            <option value="">Todos los roles</option>
            <option value="admin_global" <?= (($filtros['rol'] ?? '') === 'admin_global') ? 'selected' : '' ?>>Admin global</option>
            <option value="admin_colegio" <?= (($filtros['rol'] ?? '') === 'admin_colegio') ? 'selected' : '' ?>>Admin colegio</option>
            <option value="agente" <?= (($filtros['rol'] ?? '') === 'agente') ? 'selected' : '' ?>>Agente</option>
        </select>
        <button class="btn secondary" type="submit">Filtrar</button>
    </form>
    <table class="table" style="margin-top:8px;">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Colegios</th>
                <th>Sedes</th>
                <th>Módulos</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $fila): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($fila['usuario']) ?></td>
                    <td><?= htmlspecialchars($fila['email']) ?></td>
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
                    <td>
                        <span class="badge" style="background:<?= ($fila['estado'] === 'activo') ? '#dcfce7' : '#fee2e2' ?>;color:<?= ($fila['estado'] === 'activo') ? '#166534' : '#991b1b' ?>;">
                            <?= strtoupper($fila['estado']) ?>
                        </span>
                    </td>
                    <td><a class="btn" href="index.php?route=usuarios/detalle&id=<?= (int) $fila['id_usuario'] ?>">Ver</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($usuarios)): ?>
                <tr><td colspan="9">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" id="crear-usuario">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Crear nuevo usuario</h3>
            <p class="small" style="margin:4px 0 0;">Complete los datos y asigne los módulos permitidos.</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span class="tag" style="background:#eef2ff;color:#312e81;">Usuarios registrados: <?= $totalUsuarios ?></span>
            <button class="btn primary" form="formCrearUsuario" type="submit">Crear usuario</button>
        </div>
    </div>
    <form id="formCrearUsuario" method="post" action="index.php?route=usuarios/store" data-confirm="¿Deseas crear el nuevo usuario con los permisos seleccionados?" style="margin-top:12px;display:flex;flex-direction:column;gap:14px;" data-form-usuario>
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;">
            <div>
                <label>Nombre completo *</label>
                <input name="nombre_completo" placeholder="Ej: Laura Gómez" required>
            </div>
            <div>
                <label>Correo *</label>
                <input type="email" name="email" placeholder="correo@colegio.edu" required>
            </div>
            <div>
                <label>Usuario *</label>
                <input name="usuario" placeholder="lgomez" required>
            </div>
            <div>
                <label>Rol</label>
                <select name="rol" id="rolSelector" onchange="toggleAsignacion()">
                    <?php foreach ($rolesDisponibles as $claveRol => $nombreRol): ?>
                        <option value="<?= htmlspecialchars($claveRol) ?>" <?= $claveRol === 'agente' ? 'selected' : '' ?>><?= htmlspecialchars($nombreRol) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Contraseña *</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres">
                <small class="small" style="color:#6b7280;">Si la dejas vacía se asignará 123456 automáticamente.</small>
            </div>
            <div>
                <label>Confirmar contraseña *</label>
                <input type="password" name="password_confirm" placeholder="Repite la contraseña">
            </div>
            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
        <div class="card" style="border:1px solid #e5e7eb;background:#f9fafb;border-radius:12px;padding:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                <h4 style="margin:0;">Asignaciones</h4>
                <small class="small" style="color:#475569;">Selecciona contexto y módulos permitidos.</small>
            </div>
            <div id="asignacionColegio" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;margin-top:10px;">
                <div>
                    <label>Colegios</label>
                    <?php if (!empty($colegios)): ?>
                        <select name="permisos_colegios[]" id="colegioSelector" onchange="filtrarSedes()" multiple size="5">
                            <?php foreach ($colegios as $index => $colegio): ?>
                                <option value="<?= $colegio['id_colegio'] ?>" <?= $index === 0 ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p class="small" style="margin:4px 0 0;">No hay colegios disponibles.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label>Sedes</label>
                    <?php if (!empty($sedes)): ?>
                        <select name="permisos_sedes[]" id="sedeSelector" multiple size="6">
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?= $sede['id_sede'] ?>" data-colegio="<?= $sede['id_colegio'] ?>"><?= htmlspecialchars(($sede['colegio_nombre'] ?? 'Colegio') . ' - ' . $sede['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p class="small" style="margin:4px 0 0;">No hay sedes disponibles.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label>Módulos</label>
                    <div class="chips" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;">
                        <?php foreach ($modulosParaForm as $modulo): ?>
                            <label style="display:flex;align-items:center;gap:8px;padding:10px;border:1px solid var(--border);border-radius:10px;">
                                <input type="checkbox" class="modulo-checkbox" name="permisos_modulos[]" value="<?= htmlspecialchars($modulo['codigo']) ?>" <?= in_array($modulo['codigo'], $modulosPorDefecto, true) ? 'checked' : '' ?>>
                                <span style="font-weight:600;"><?= htmlspecialchars($modulo['nombre']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($modulos)): ?>
                        <p class="small" style="margin:6px 0 0;color:#6b7280;">No hay módulos configurados en base de datos. Se muestran los módulos base para su selección.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:6px;">
            <button class="btn primary" type="submit">Guardar usuario</button>
        </div>
    </form>
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

toggleAsignacion();
filtrarSedes();
</script>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
