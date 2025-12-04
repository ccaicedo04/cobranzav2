<?php
use Core\Helpers;

$title = 'Configuración institucional';
$pageTitle = 'Configuración del colegio';
$breadcrumbs = 'Configuración';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=configuracion/store" data-confirm="¿Deseas guardar la nueva configuración institucional?" class="config-form">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">

        <section class="config-block">
            <header>
                <h2>Correo electrónico institucional</h2>
                <p>Datos SMTP utilizados para enviar notificaciones formales desde el área de cartera.</p>
            </header>
            <div class="grid">
                <label>
                    <span>SMTP Host</span>
                    <input name="smtp_host" value="<?= htmlspecialchars($configuracion['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                </label>
                <label>
                    <span>SMTP Puerto</span>
                    <input name="smtp_puerto" value="<?= htmlspecialchars($configuracion['smtp_puerto'] ?? '') ?>" placeholder="587">
                </label>
                <label>
                    <span>SMTP Usuario</span>
                    <input name="smtp_usuario" value="<?= htmlspecialchars($configuracion['smtp_usuario'] ?? '') ?>" placeholder="correo@colegio.edu.co">
                </label>
                <label>
                    <span>SMTP Contraseña</span>
                    <input type="password" name="smtp_password" value="<?= htmlspecialchars($configuracion['smtp_password'] ?? '') ?>" placeholder="********">
                </label>
            </div>
        </section>

        <section class="config-block">
            <header>
                <h2>Integración oficial de Twilio</h2>
                <p>Completa las credenciales del colegio para habilitar el envío y recepción de mensajes reales vía WhatsApp y SMS.</p>
            </header>
            <div class="grid">
                <label>
                    <span>Account SID</span>
                    <input name="twilio_account_sid" value="<?= htmlspecialchars($configuracion['twilio_account_sid'] ?? '') ?>" placeholder="ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                </label>
                <label>
                    <span>Auth Token</span>
                    <input name="twilio_auth_token" value="<?= htmlspecialchars($configuracion['twilio_auth_token'] ?? '') ?>" placeholder="••••••••••">
                </label>
                <label>
                    <span>Número WhatsApp (formato E.164)</span>
                    <input name="twilio_whatsapp_from" value="<?= htmlspecialchars($configuracion['twilio_whatsapp_from'] ?? '') ?>" placeholder="+12566374335">
                </label>
                <label>
                    <span>Número SMS remitente</span>
                    <input name="twilio_sms_from" value="<?= htmlspecialchars($configuracion['twilio_sms_from'] ?? '') ?>" placeholder="+12566374335">
                </label>
                <label>
                    <span>Código país por defecto</span>
                    <input name="twilio_default_country" value="<?= htmlspecialchars($configuracion['twilio_default_country'] ?? '+57') ?>" placeholder="+57">
                </label>
                <label>
                    <span>Status Callback (opcional)</span>
                    <input name="twilio_status_callback" value="<?= htmlspecialchars($configuracion['twilio_status_callback'] ?? '') ?>" placeholder="https://example.com/twilio/status">
                </label>
                <label>
                    <span>Webhook recepción (opcional)</span>
                    <input name="twilio_incoming_webhook" value="<?= htmlspecialchars($configuracion['twilio_incoming_webhook'] ?? '') ?>" placeholder="https://example.com/webhooks/twilio">
                </label>
            </div>
            <p class="helper">Recuerda activar el sandbox de WhatsApp en Twilio y apuntar la URL <code><?= htmlspecialchars(Helpers::baseUrl('index.php?route=/webhooks/twilio')) ?></code> en la configuración de mensajes entrantes.</p>
        </section>

        <section class="config-block">
            <header>
                <h2>Integraciones heredadas (opcional)</h2>
                <p>Estos campos permiten conservar integraciones anteriores basadas en API propias.</p>
            </header>
            <div class="grid">
                <label>
                    <span>API WhatsApp Key</span>
                    <input name="whatsapp_api_key" value="<?= htmlspecialchars($configuracion['whatsapp_api_key'] ?? '') ?>">
                </label>
                <label>
                    <span>Endpoint WhatsApp</span>
                    <input name="whatsapp_endpoint" value="<?= htmlspecialchars($configuracion['whatsapp_endpoint'] ?? '') ?>">
                </label>
                <label>
                    <span>API SMS Key</span>
                    <input name="sms_api_key" value="<?= htmlspecialchars($configuracion['sms_api_key'] ?? '') ?>">
                </label>
                <label>
                    <span>Endpoint SMS</span>
                    <input name="sms_endpoint" value="<?= htmlspecialchars($configuracion['sms_endpoint'] ?? '') ?>">
                </label>
            </div>
        </section>

        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <button class="btn" type="submit">Guardar configuración</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
