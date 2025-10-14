<?php
$title = 'Iniciar sesión';
$fullWidth = true;
include __DIR__ . '/../_partials/header.php';
?>
<div class="login-wrap" style="margin-top:60px;">
    <div class="login-left">
        <h2 class="login-title">INICIAR SESIÓN</h2>
        <?php if (!empty($error)): ?>
            <div class="card" style="background:#fee2e2;border-color:#fecaca;color:#991b1b;margin-bottom:16px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="post" action="index.php?route=auth/login">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? Core\Helpers::csrfToken()) ?>">
            <label>Usuario</label>
            <input name="usuario" placeholder="Ingresa tu usuario" required>
            <div style="height:10px"></div>
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="Ingresa tu contraseña" required>
            <div style="display:flex;justify-content:space-between;align-items:center;margin:12px 0 16px">
                <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" style="width:auto"> Recordarme</label>
                <a href="#">¿Olvidaste tu contraseña?</a>
            </div>
            <button class="btn" type="submit">Entrar</button>
        </form>
        <p class="small" style="margin-top:14px">Ingresa tus credenciales para continuar.</p>
    </div>
    <div class="login-right">
        <div style="text-align:center">
            <img src="images/logo-yoyjo.png" alt="Logo demo" style="width:300px;height:auto;opacity:.9">
            <div style='font-weight:800;font-size:22px;margin-bottom:8px'>Sistema de Cobranza</div>
            <p>Gestione cartera, pagos y comunicaciones en una sola plataforma.</p>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
