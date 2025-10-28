<?php
$isEdit = !empty($plantilla);
$title = $isEdit ? 'Editar plantilla' : 'Nueva plantilla';
$pageTitle = $title;
$breadcrumbs = 'Parametrización / Plantillas / ' . ($isEdit ? 'Editar' : 'Crear');
include __DIR__ . '/../../_partials/header.php';

$canales = [
    'email' => 'Correo electrónico',
    'whatsapp' => 'WhatsApp',
    'sms' => 'SMS',
    'llamada' => 'Llamada telefónica',
];
?>
<form class="card" method="post" action="index.php?route=plantillas/<?= $isEdit ? 'update' : 'store' ?>" style="margin-bottom:18px;">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id_plantilla" value="<?= (int) $plantilla['id_plantilla'] ?>">
    <?php endif; ?>
    <div class="two">
        <div>
            <label>Nombre de la plantilla</label>
            <input name="nombre" value="<?= htmlspecialchars($plantilla['nombre'] ?? '') ?>" required>
        </div>
        <div>
            <label>Canal</label>
            <select name="canal" required>
                <?php foreach ($canales as $valor => $label): ?>
                    <option value="<?= $valor ?>" <?= (($plantilla['canal'] ?? 'email') === $valor) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="two">
        <div>
            <label>Descripción interna</label>
            <input name="descripcion" value="<?= htmlspecialchars($plantilla['descripcion'] ?? '') ?>" placeholder="Uso, objetivo o segmentación">
        </div>
        <div>
            <label>Asunto (para correo electrónico)</label>
            <input name="asunto_default" value="<?= htmlspecialchars($plantilla['asunto_default'] ?? '') ?>" placeholder="Ej: Recordatorio de pago — {{estudiante_nombre}}">
        </div>
    </div>
    <div class="two" style="align-items:flex-start;">
        <div>
            <label>Variables disponibles</label>
            <textarea name="variables" rows="4" placeholder="responsable_nombre, estudiante_nombre, saldo_pendiente, fecha_vencimiento" style="resize:vertical;"><?= htmlspecialchars($plantilla['variables'] ?? '') ?></textarea>
            <p class="small">Separa cada variable por coma o salto de línea. En el cuerpo podrás referenciarlas como <code>{{variable}}</code>.</p>
        </div>
        <div>
            <label>Estado</label>
            <select name="estado">
                <option value="activo" <?= ($plantilla['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activa</option>
                <option value="inactivo" <?= ($plantilla['estado'] ?? 'activo') === 'inactivo' ? 'selected' : '' ?>>Inactiva</option>
            </select>
            <p class="small">Solo las plantillas activas estarán disponibles para los usuarios del módulo de comunicaciones.</p>
        </div>
    </div>
    <div>
        <label>Cuerpo de la plantilla</label>
        <textarea name="cuerpo_html" rows="14" required style="font-family:'JetBrains Mono',monospace;resize:vertical;"><?= htmlspecialchars($plantilla['cuerpo_html'] ?? '') ?></textarea>
        <p class="small">Puedes utilizar HTML básico para correos o texto plano para WhatsApp/SMS. Recuerda incluir las variables encerradas entre dobles llaves.</p>
    </div>
    <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;">
        <a class="btn secondary" href="index.php?route=plantillas">Cancelar</a>
        <button class="btn" type="submit">Guardar</button>
    </div>
</form>
<div class="card">
    <h3>Variables sugeridas</h3>
    <p class="small">Estas variables están disponibles en la mayoría de procesos de cobranza:</p>
    <ul class="small" style="margin:0;padding-left:18px;line-height:1.6;">
        <li><code>{{responsable_nombre}}</code>, <code>{{responsable_documento}}</code></li>
        <li><code>{{estudiante_nombre}}</code>, <code>{{codigo_estudiante}}</code>, <code>{{grado}}</code></li>
        <li><code>{{saldo_pendiente}}</code>, <code>{{fecha_vencimiento}}</code>, <code>{{concepto}}</code></li>
        <li><code>{{colegio_nombre}}</code>, <code>{{sede_nombre}}</code>, <code>{{telefono_contacto}}</code></li>
    </ul>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
