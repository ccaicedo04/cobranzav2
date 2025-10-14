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
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Colegio</th><th>Sede</th><th>Rol</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($usuarios as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($fila['usuario']) ?></td>
                        <td><?= htmlspecialchars($fila['colegio_nombre'] ?? 'No aplica') ?></td>
                        <td><?= htmlspecialchars($fila['sede_nombre'] ?? 'No aplica') ?></td>
                        <td><?= htmlspecialchars(strtoupper($fila['rol'])) ?></td>
                        <td><?= htmlspecialchars($fila['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="6">No hay usuarios registrados.</td></tr>
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
                <?php if (!empty($colegios)): ?>
                    <label>Colegio</label>
                    <select name="id_colegio" id="colegioSelector" onchange="filtrarSedes()">
                        <option value="">Seleccione</option>
                        <?php foreach ($colegios as $colegio): ?>
                            <option value="<?= $colegio['id_colegio'] ?>"><?= htmlspecialchars($colegio['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="id_colegio" value="<?= htmlspecialchars($usuario['id_colegio'] ?? '') ?>">
                <?php endif; ?>
                <label>Sede</label>
                <select name="id_sede" id="sedeSelector">
                    <option value="">Seleccione</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id_sede'] ?>" data-colegio="<?= $sede['id_colegio'] ?>"><?= htmlspecialchars($sede['colegio_nombre'] . ' - ' . $sede['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
    if (rol === 'admin_global') {
        asignacion.style.display = 'none';
    } else {
        asignacion.style.display = 'block';
    }
}

function filtrarSedes() {
    const colegio = document.getElementById('colegioSelector');
    const sedeSelector = document.getElementById('sedeSelector');
    const colegioId = colegio ? colegio.value : '';
    [...sedeSelector.options].forEach(option => {
        if (!option.value) {
            option.hidden = false;
            return;
        }
        const pertenece = option.dataset.colegio;
        option.hidden = colegioId && pertenece !== colegioId;
    });
    if (colegioId) {
        const visible = [...sedeSelector.options].find(option => !option.hidden && option.value);
        if (visible) {
            sedeSelector.value = visible.value;
        }
    }
}

toggleAsignacion();
</script>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
