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
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['rol']) ?></td>
                        <td><?= htmlspecialchars($usuario['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="5">No hay usuarios registrados.</td></tr>
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
            <select name="rol">
                <option value="admin_global">Administrador Global</option>
                <option value="admin_colegio">Administrador Colegio</option>
                <option value="agente" selected>Agente</option>
            </select>
            <label>Estado</label>
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
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
