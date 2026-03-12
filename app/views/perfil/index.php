<?php
$title = 'Perfil de usuario';
$pageTitle = 'Mi perfil';
$breadcrumbs = 'Perfil';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=perfil/actualizar">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <label>Nombre completo</label>
        <input name="nombre_completo" value="<?= htmlspecialchars($usuario['nombre_completo'] ?? '') ?>" required>
        <label>Correo electrónico</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
        <label>Usuario</label>
        <input value="<?= htmlspecialchars($usuario['usuario'] ?? '') ?>" disabled>
        <label>Nueva contraseña</label>
        <input type="password" name="password" placeholder="Opcional">
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <button class="btn" type="submit">Actualizar perfil</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
