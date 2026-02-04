<?php
$title = 'Iniciar sesión';
$fullWidth = true;
include __DIR__ . '/../_partials/header.php';
?>

<div class="login-page">
<div class="login-shell">
    <div class="login-hero">
        <div class="login-hero-content">
            <div class="brand-mark">
                <img src="images/logo-yoyjo.png" alt="Logo" class="brand-logo">
                <div>
                    <strong>COBRANZA Escolar</strong>
                    <span>Multi-colegio · Multi-sede</span>
                </div>
            </div>
            <h1>Centraliza tu cartera y agiliza la gestión</h1>
            <p>Controla responsables, estudiantes y deudas en un panel unificado. Optimiza tus ciclos de recaudo con visibilidad en tiempo real.</p>
            <ul>
                <li>Acceso seguro para administradores y gestores.</li>
                <li>Dashboard de cobranzas y reportes listos para descargar.</li>
                <li>Carga masiva Phidias con trazabilidad completa.</li>
            </ul>
        </div>
    </div>
    <div class="login-card">
        <div class="login-card-header">
            <div>
                <p class="eyebrow">Bienvenido</p>
                <h2>Inicia sesión</h2>
                <p class="subtext">Ingresa tus credenciales para continuar.</p>
            </div>
            <div class="tag">Acceso seguro</div>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form class="login-form" method="post" action="index.php?route=auth/login">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? Core\Helpers::csrfToken()) ?>">
            <label>Usuario
                <input name="usuario" placeholder="Ingresa tu usuario" required autocomplete="username">
            </label>
            <label>Contraseña
                <input type="password" name="password" placeholder="Ingresa tu contraseña" required autocomplete="current-password">
            </label>
            <div class="login-form-footer">
                <label class="checkbox-inline">
                    <input type="checkbox" name="remember" value="1"> Recordarme
                </label>
                <a class="muted" href="#">¿Olvidaste tu contraseña?</a>
            </div>
            <button class="btn" type="submit">Entrar</button>
        </form>
        <p class="small" style="margin-top:10px">¿No tienes acceso? Contacta al administrador del colegio.</p>
    </div>
</div>
</div>

<?php include __DIR__ . '/../_partials/footer.php'; ?>
