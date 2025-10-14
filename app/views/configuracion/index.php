<?php
$title = 'Configuración institucional';
$pageTitle = 'Configuración del colegio';
$breadcrumbs = 'Configuración';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=configuracion/store">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <div class="two">
            <div>
                <label>SMTP Host</label>
                <input name="smtp_host" value="<?= htmlspecialchars($configuracion['smtp_host'] ?? '') ?>">
            </div>
            <div>
                <label>SMTP Puerto</label>
                <input name="smtp_puerto" value="<?= htmlspecialchars($configuracion['smtp_puerto'] ?? '') ?>">
            </div>
        </div>
        <div class="two">
            <div>
                <label>SMTP Usuario</label>
                <input name="smtp_usuario" value="<?= htmlspecialchars($configuracion['smtp_usuario'] ?? '') ?>">
            </div>
            <div>
                <label>SMTP Contraseña</label>
                <input type="password" name="smtp_password" value="<?= htmlspecialchars($configuracion['smtp_password'] ?? '') ?>">
            </div>
        </div>
        <div class="two">
            <div>
                <label>API WhatsApp Key</label>
                <input name="whatsapp_api_key" value="<?= htmlspecialchars($configuracion['whatsapp_api_key'] ?? '') ?>">
            </div>
            <div>
                <label>Endpoint WhatsApp</label>
                <input name="whatsapp_endpoint" value="<?= htmlspecialchars($configuracion['whatsapp_endpoint'] ?? '') ?>">
            </div>
        </div>
        <div class="two">
            <div>
                <label>API SMS Key</label>
                <input name="sms_api_key" value="<?= htmlspecialchars($configuracion['sms_api_key'] ?? '') ?>">
            </div>
            <div>
                <label>Endpoint SMS</label>
                <input name="sms_endpoint" value="<?= htmlspecialchars($configuracion['sms_endpoint'] ?? '') ?>">
            </div>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <button class="btn" type="submit">Guardar configuración</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
