<?php
$title = 'Plantillas de comunicación';
$pageTitle = 'Plantillas de comunicación';
$breadcrumbs = 'Parametrización / Plantillas';
include __DIR__ . '/../../_partials/header.php';

$formatCanal = static function (string $canal): string {
    return match ($canal) {
        'email' => 'Correo electrónico',
        'whatsapp' => 'WhatsApp',
        'sms' => 'SMS',
        'llamada' => 'Llamada telefónica',
        default => ucfirst($canal),
    };
};

?>
<div class="card" style="margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
    <div>
        <h3 style="margin:0;">Catálogo de plantillas</h3>
        <p class="small" style="margin:4px 0 0;max-width:520px;">Define mensajes consistentes para tus campañas de cobranza y reutilízalos desde el módulo de comunicaciones. Cada plantilla puede parametrizar variables que el sistema sustituirá automáticamente.</p>
    </div>
    <a class="btn" href="index.php?route=plantillas/create">Nueva plantilla</a>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Canal</th>
                <th>Estado</th>
                <th>Variables disponibles</th>
                <th style="width:140px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plantillas as $plantilla): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($plantilla['nombre']) ?></strong>
                        <div class="small" style="color:#4b5563;max-width:320px;"><?= htmlspecialchars($plantilla['descripcion'] ?? '') ?></div>
                    </td>
                    <td><?= htmlspecialchars($formatCanal($plantilla['canal'])) ?></td>
                    <td>
                        <?php if (($plantilla['estado'] ?? '') === 'activo'): ?>
                            <span class="tag success">Activa</span>
                        <?php else: ?>
                            <span class="tag">Inactiva</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $variables = array_filter(array_map('trim', explode(',', (string) ($plantilla['variables'] ?? ''))));
                        if (!$variables): ?>
                            <span class="tag">Sin variables</span>
                        <?php else: ?>
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                <?php foreach ($variables as $variable): ?>
                                    <span class="chip">{{<?= htmlspecialchars($variable) ?>}}</span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:8px;justify-content:flex-end;">
                            <a class="btn ghost sm" href="index.php?route=plantillas/edit&id=<?= (int) $plantilla['id_plantilla'] ?>">Editar</a>
                            <?php if (($plantilla['estado'] ?? '') === 'activo'): ?>
                                <form method="post" action="index.php?route=plantillas/delete" data-confirm="¿Deseas desactivar esta plantilla?" style="margin:0;">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $plantilla['id_plantilla'] ?>">
                                    <button class="btn secondary sm" type="submit">Desactivar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <details style="margin-top:10px;">
                            <summary class="small" style="cursor:pointer;color:#1d4ed8;">Ver contenido</summary>
                            <div class="preview" style="margin-top:10px;padding:12px;border:1px solid #d1d5db;border-radius:12px;background:#f8fafc;max-height:260px;overflow:auto;">
                                <?php if ($plantilla['canal'] === 'email'): ?>
                                    <?= $plantilla['cuerpo_html'] ?? '' ?>
                                <?php else: ?>
                                    <pre style="white-space:pre-wrap;font-family:'Inter',sans-serif;line-height:1.5;margin:0;"><?= htmlspecialchars($plantilla['cuerpo_html'] ?? '') ?></pre>
                                <?php endif; ?>
                            </div>
                        </details>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($plantillas)): ?>
                <tr><td colspan="5">Aún no has configurado plantillas personalizadas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
