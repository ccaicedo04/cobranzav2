<?php
$title = 'Iniciar sesión';
$fullWidth = true;
include __DIR__ . '/../_partials/header.php';
?>

<div class="login-page">
    <div class="login-shell">
        <div class="login-hero gradient-primary">
            <div class="login-hero-overlay"></div>
            <div class="login-hero-content">
                <div class="brand-mark">
                    <div class="brand-logo-chip">🏛️</div>
                    <div>
                        <strong>COBRANZA</strong>
                        <span>Gestión de cartera escolar</span>
                    </div>
                </div>
                <h1>Gestión de cartera escolar inteligente</h1>
                <p>Centraliza, automatiza y optimiza el recaudo con trazabilidad total y comunicación multicanal.</p>
                <ul>
                    <li>Dashboard ejecutivo con KPIs en tiempo real.</li>
                    <li>Comunicación multicanal auditada.</li>
                    <li>Reportes y exportes listos para auditoría.</li>
                    <li>Carga masiva con validaciones automatizadas.</li>
                </ul>
                <div class="login-hero-stats">
                    <div>
                        <strong>98%</strong>
                        <span>Reducción en mora</span>
                    </div>
                    <div>
                        <strong>+50</strong>
                        <span>Colegios activos</span>
                    </div>
                    <div>
                        <strong>24/7</strong>
                        <span>Disponibilidad</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-card">
            <div class="login-card-header">
                <div>
                    <p class="eyebrow">Bienvenido de nuevo</p>
                    <h2>Inicia sesión</h2>
                    <p class="subtext">Ingresa tus credenciales para acceder al sistema.</p>
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
                <label class="form-label">Usuario
                    <input class="form-input" name="usuario" placeholder="usuario@colegio.edu.co" required autocomplete="username">
                </label>
                <label class="form-label">Contraseña
                    <input class="form-input" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                </label>
                <div class="login-form-footer">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="remember" value="1"> Recordarme
                    </label>
                    <a class="muted" href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <button class="btn" type="submit">Iniciar sesión</button>
            </form>
            <p class="small" style="margin-top:10px">¿No tienes acceso? Contacta al administrador del colegio.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../_partials/footer.php'; ?>
