<?php
$title = 'Comunicaciones';
$pageTitle = 'Centro de comunicaciones';
$breadcrumbs = 'Cobranzas / Comunicaciones';
include __DIR__ . '/../_partials/header.php';

$selectedResponsable = $selectedResponsable ?? 0;
$selectedCanal = $selectedCanal ?? 'email';
$selectedPlantilla = $selectedPlantilla ?? 0;
$status = $status ?? null;
$statusMessage = $statusMessage ?? null;

$estudiantesPorResponsable = [];
foreach ($estudiantes as $estudiante) {
    $responsableId = (int) ($estudiante['id_responsable'] ?? 0);
    if (!isset($estudiantesPorResponsable[$responsableId])) {
        $estudiantesPorResponsable[$responsableId] = [];
    }
    $estudiantesPorResponsable[$responsableId][] = $estudiante;
}

$plantillasPorCanal = [];
foreach ($plantillas as $plantilla) {
    $plantillasPorCanal[$plantilla['canal']][] = $plantilla;
}

$plantillaPorId = [];
foreach ($plantillas as $plantilla) {
    $plantillaPorId[(int) $plantilla['id_plantilla']] = $plantilla;
}

$defaultEstudiante = null;
if (isset($estudiantesPorResponsable[$selectedResponsable])) {
    $defaultEstudiante = $estudiantesPorResponsable[$selectedResponsable][0]['id_estudiante'] ?? null;
}

$datasetJson = json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<div class="card" style="margin-bottom:18px;display:flex;flex-direction:column;gap:12px;">
    <div>
        <h3 style="margin:0;">Gestiona tus comunicaciones</h3>
        <p class="small" style="margin:4px 0 0;max-width:760px;">
            Selecciona un responsable, elige el canal y aplica una plantilla preconfigurada para enviar recordatorios profesionales en segundos.
            El panel actualizará en tiempo real la cartera y el historial asociado para mantener un control integral de la gestión.
        </p>
    </div>
    <div class="comms-highlight">
        <div>
            <span class="label">Plantillas activas</span>
            <strong><?= count($plantillas) ?></strong>
        </div>
        <div>
            <span class="label">Responsables cargados</span>
            <strong><?= count($responsables) ?></strong>
        </div>
        <div>
            <span class="label">Comunicaciones registradas</span>
            <strong><?= count($comunicaciones) ?></strong>
        </div>
    </div>
</div>

<?php if ($status): ?>
    <div class="alert <?= $status === 'error' ? 'error' : 'success' ?>">
        <strong><?= $status === 'error' ? 'Hubo un inconveniente' : 'Acción completada' ?>:</strong>
        <span><?= htmlspecialchars($statusMessage ?: 'Gestión procesada correctamente.') ?></span>
    </div>
<?php endif; ?>

<div id="comunicacionesApp" data-selected-responsable="<?= (int) $selectedResponsable ?>" data-selected-canal="<?= htmlspecialchars($selectedCanal) ?>" data-selected-plantilla="<?= (int) $selectedPlantilla ?>" class="comms-shell">
    <div class="comms-layout">
        <form class="card comms-form" method="post" action="index.php?route=comunicaciones/store" data-confirm="¿Deseas registrar la comunicación con la información diligenciada?">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <div class="wizard-body">
                <div class="wizard-fields">
                    <section class="wizard-step">
                        <header>
                            <span class="step">1</span>
                            <div>
                                <h4>Selecciona el responsable y estudiante</h4>
                                <p class="small">El sistema carga automáticamente la cartera y los datos de contacto.</p>
                            </div>
                        </header>
                        <div class="two">
                            <div>
                                <label for="commsResponsable">Responsable financiero</label>
                                <select name="id_responsable" id="commsResponsable" required>
                                    <?php foreach ($responsables as $responsable): ?>
                                        <option value="<?= (int) $responsable['id_responsable'] ?>" <?= $selectedResponsable === (int) $responsable['id_responsable'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($responsable['nombre_completo']) ?> — <?= htmlspecialchars($responsable['numero_documento']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="commsEstudiante">Estudiante asociado</label>
                                <select name="id_estudiante" id="commsEstudiante">
                                    <option value="">Todos los estudiantes del responsable</option>
                                    <?php foreach ($estudiantes as $estudiante): ?>
                                        <option value="<?= (int) $estudiante['id_estudiante'] ?>" data-responsable="<?= (int) $estudiante['id_responsable'] ?>" <?= ($defaultEstudiante && $defaultEstudiante === (int) $estudiante['id_estudiante']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estudiante['nombre_completo']) ?> — <?= htmlspecialchars($estudiante['grado'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="resume" data-resume>
                            <div>
                                <span>Saldo pendiente</span>
                                <strong data-resume-saldo>$ 0</strong>
                            </div>
                            <div>
                                <span>Deudas activas</span>
                                <strong data-resume-deudas>0</strong>
                            </div>
                            <div>
                                <span>Próximo vencimiento</span>
                                <strong data-resume-vencimiento>—</strong>
                            </div>
                        </div>
                    </section>

                    <section class="wizard-step">
                        <header>
                            <span class="step">2</span>
                            <div>
                                <h4>Define el canal y plantilla</h4>
                                <p class="small">Las plantillas son parametrizables y se adaptan a cada canal de comunicación.</p>
                            </div>
                        </header>
                        <div class="two">
                            <div>
                                <label for="commsCanal">Canal de contacto</label>
                                <select name="canal" id="commsCanal">
                                    <option value="email" <?= $selectedCanal === 'email' ? 'selected' : '' ?>>Correo electrónico</option>
                                    <option value="whatsapp" <?= $selectedCanal === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                                    <option value="sms" <?= $selectedCanal === 'sms' ? 'selected' : '' ?>>SMS</option>
                                    <option value="llamada" <?= $selectedCanal === 'llamada' ? 'selected' : '' ?>>Llamada telefónica</option>
                                </select>
                            </div>
                            <div>
                                <label for="commsPlantilla">Plantilla</label>
                                <select name="id_plantilla" id="commsPlantilla">
                                    <option value="">Sin plantilla predefinida</option>
                                    <?php foreach ($plantillas as $plantilla): ?>
                                        <option value="<?= (int) $plantilla['id_plantilla'] ?>" data-canal="<?= htmlspecialchars($plantilla['canal']) ?>" <?= $selectedPlantilla === (int) $plantilla['id_plantilla'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($plantilla['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="wizard-step">
                        <header>
                            <span class="step">3</span>
                            <div>
                                <h4>Personaliza el mensaje</h4>
                                <p class="small">Puedes editar asunto y cuerpo antes de enviar o registrar la gestión.</p>
                            </div>
                        </header>
                        <div class="two">
                            <div>
                                <label for="commsAsunto">Asunto</label>
                                <input id="commsAsunto" name="asunto" placeholder="Ej: Recordatorio de pago — {{estudiante_nombre}}" required>
                            </div>
                            <div>
                                <label for="commsResultado">Resultado esperado</label>
                                <input id="commsResultado" name="resultado" placeholder="Ej: Compromiso de pago confirmado">
                            </div>
                        </div>
                        <div>
                            <label for="commsMensaje">Mensaje</label>
                            <textarea id="commsMensaje" name="mensaje" rows="10" required style="resize:vertical;"></textarea>
                            <p class="small">Las variables como <code>{{responsable_nombre}}</code> se reemplazarán automáticamente con los datos reales.</p>
                        </div>
                    </section>
                </div>
                <aside class="wizard-preview">
                    <h4>Previsualización</h4>
                    <div class="preview-meta">
                        <div>
                            <span class="label">Enviar a</span>
                            <strong data-preview-destinatario>Selecciona un responsable</strong>
                        </div>
                        <div>
                            <span class="label">Canal</span>
                            <strong data-preview-canal><?= htmlspecialchars(ucfirst($selectedCanal)) ?></strong>
                        </div>
                    </div>
                    <div class="email-preview" data-preview>
                        <div class="placeholder">Selecciona un responsable y una plantilla para visualizar el contenido.</div>
                    </div>
                </aside>
            </div>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:12px;margin-top:18px;">
                <a class="btn secondary" href="index.php?route=comunicaciones">Cancelar</a>
                <button class="btn" type="submit">Registrar gestión</button>
            </div>
        </form>

        <div class="card comms-history">
            <header class="history-header">
                <div>
                    <h3>Historial reciente</h3>
                    <p class="small">Revisa las últimas comunicaciones registradas con responsables y estudiantes.</p>
                </div>
            </header>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Responsable</th>
                            <th>Canal</th>
                            <th>Estado</th>
                            <th>Asunto</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comunicaciones as $registro): ?>
                            <?php
                            $responsableNombre = '';
                            foreach ($responsables as $responsable) {
                                if ((int) $responsable['id_responsable'] === (int) $registro['id_responsable']) {
                                    $responsableNombre = $responsable['nombre_completo'];
                                    break;
                                }
                            }
                            $estado = strtolower((string) ($registro['estado_envio'] ?? ''));
                            $estadoLabel = match ($estado) {
                                'enviado' => ['label' => 'Enviado', 'class' => 'success'],
                                'error' => ['label' => 'Error', 'class' => 'danger'],
                                'registrado' => ['label' => 'Registrado', 'class' => 'neutral'],
                                default => ['label' => ucfirst($estado ?: 'Pendiente'), 'class' => 'neutral'],
                            };
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($registro['fecha_envio']) ?></td>
                                <td><?= htmlspecialchars($responsableNombre ?: $registro['id_responsable']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($registro['canal'])) ?></td>
                                <td><span class="tag <?= $estadoLabel['class'] ?>"><?= htmlspecialchars($estadoLabel['label']) ?></span></td>
                                <td><?= htmlspecialchars($registro['asunto'] ?? '') ?></td>
                                <td><?= htmlspecialchars($registro['resultado'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($comunicaciones)): ?>
                            <tr><td colspan="6">Aún no hay comunicaciones registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.COMMS_DATA = <?= $datasetJson ?>;
window.COMMS_DATA = window.COMMS_DATA || {};
window.COMMS_DATA.plantillas = <?= json_encode($plantillas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
