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

$selectedResponsableInfo = null;
foreach ($responsables as $item) {
    if ((int) $item['id_responsable'] === (int) $selectedResponsable) {
        $selectedResponsableInfo = $item;
        break;
    }
}

$defaultEstudiante = $defaultEstudiante ?? null;
$datasetJson = json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<div class="card comms-header">
    <div>
        <h3>Gestiona tus comunicaciones en tiempo real</h3>
        <p class="small">
            Envía correos, mensajes de WhatsApp o SMS y registra llamadas desde una interfaz inspirada en chats.
            Visualiza el historial con el responsable, recibe adjuntos y mantén el control de la cartera en un solo lugar.
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

<div id="comunicacionesApp"
     data-selected-responsable="<?= (int) $selectedResponsable ?>"
     data-selected-canal="<?= htmlspecialchars($selectedCanal) ?>"
     data-selected-plantilla="<?= (int) $selectedPlantilla ?>"
     class="chat-shell">
    <form class="chat-form" method="post" action="index.php?route=comunicaciones/store" enctype="multipart/form-data"
          data-confirm="¿Deseas registrar la comunicación con la información diligenciada?">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <div class="chat-grid">
            <aside class="chat-panel chat-panel--notifications">
                <header>
                    <h4>Chats recientes</h4>
                    <p class="small">Conversaciones activas con responsables por WhatsApp o SMS.</p>
                </header>
                <ul class="chat-notifications" data-chat-notifications>
                    <?php foreach ($notifications as $alerta): ?>
                        <li>
                            <button type="button"
                                    class="chat-notification"
                                    data-responsable="<?= (int) $alerta['id_responsable'] ?>"
                                    data-canal="<?= htmlspecialchars($alerta['canal'] ?? 'whatsapp') ?>">
                                <div class="chat-notification-head">
                                    <span class="chat-notification-name"><?= htmlspecialchars($alerta['responsable'] ?? ('Responsable #' . $alerta['id_responsable'])) ?></span>
                                    <span class="chat-notification-time"><?= htmlspecialchars($alerta['fecha_formateada'] ?: $alerta['fecha']) ?></span>
                                </div>
                                <div class="chat-notification-body"><?= htmlspecialchars($alerta['mensaje'] ?? '') ?></div>
                                <span class="chat-notification-tag"><?= strtoupper(htmlspecialchars($alerta['canal'] ?? 'whatsapp')) ?></span>
                            </button>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($notifications)): ?>
                        <li class="chat-notification empty">Sin novedades recientes.</li>
                    <?php endif; ?>
                </ul>
            </aside>

            <section class="chat-panel chat-panel--main">
                <header class="chat-panel-header">
                    <div>
                        <span class="label">Conversación activa</span>
                        <h4 data-chat-responsable><?= htmlspecialchars($selectedResponsableInfo['nombre_completo'] ?? 'Selecciona un responsable') ?></h4>
                        <span class="small" data-chat-contact>
                            <?php if (!empty($selectedResponsableInfo)): ?>
                                <?= htmlspecialchars(trim(($selectedResponsableInfo['telefono'] ?? '') . ($selectedResponsableInfo['correo'] ? ' • ' . $selectedResponsableInfo['correo'] : ''))) ?>
                            <?php else: ?>
                                Selecciona un responsable para ver su información de contacto.
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="chat-status">
                        <span class="status-pill <?= $twilioConfigured ? 'online' : 'offline' ?>">
                            <?= $twilioConfigured ? 'Twilio conectado' : 'Twilio sin configurar' ?>
                        </span>
                    </div>
                </header>

                <div class="chat-thread" data-chat-thread>
                    <?php foreach ($conversation as $mensaje): ?>
                        <?php
                        $origen = ($mensaje['origen'] ?? '') === 'responsable' ? 'incoming' : 'outgoing';
                        $autor = $origen === 'incoming' ? 'Responsable' : 'Equipo de cartera';
                        ?>
                        <div class="chat-message <?= $origen ?>">
                            <div class="chat-message-meta">
                                <span><?= htmlspecialchars($autor) ?></span>
                                <span><?= htmlspecialchars($mensaje['fecha_formateada'] ?: $mensaje['fecha']) ?></span>
                            </div>
                            <div class="chat-message-body">
                                <?= nl2br(htmlspecialchars($mensaje['texto'] ?? '')) ?>
                            </div>
                            <?php if (!empty($mensaje['adjuntos'])): ?>
                                <div class="chat-message-files">
                                    <?php foreach ($mensaje['adjuntos'] as $adjunto): ?>
                                        <a href="<?= htmlspecialchars($adjunto['url'] ?? '#') ?>" target="_blank" rel="noopener" download>
                                            <?= htmlspecialchars($adjunto['nombre'] ?? 'Descargar adjunto') ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($mensaje['detalle'])): ?>
                                <div class="chat-message-status"><?= htmlspecialchars($mensaje['detalle']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($conversation)): ?>
                        <div class="chat-thread-empty">Aún no hay mensajes registrados para este canal.</div>
                    <?php endif; ?>
                </div>

                <div class="chat-composer">
                    <label for="commsMensaje">Escribe tu mensaje</label>
                    <textarea id="commsMensaje" name="mensaje" rows="4"
                              placeholder="Saluda al responsable o reutiliza una plantilla personalizada"
                              required></textarea>
                    <p class="small">Las variables como <code>{{responsable_nombre}}</code> se reemplazarán automáticamente con los datos reales.</p>
                    <div class="chat-composer-footer">
                        <label class="chat-attachment">
                            <input type="file" name="adjuntos[]" multiple data-chat-attachments>
                            <span>Adjuntar archivos</span>
                        </label>
                        <div class="chat-composer-actions">
                            <button type="button" class="btn secondary" data-clear-mensaje>Limpiar</button>
                            <button class="btn" type="submit">Enviar / Registrar</button>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="chat-panel chat-panel--details">
                <div class="chat-section">
                    <label for="commsResponsable">Responsable financiero</label>
                    <select name="id_responsable" id="commsResponsable" required>
                        <?php foreach ($responsables as $responsable): ?>
                            <option value="<?= (int) $responsable['id_responsable'] ?>" <?= $selectedResponsable === (int) $responsable['id_responsable'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($responsable['nombre_completo']) ?> — <?= htmlspecialchars($responsable['numero_documento']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="chat-section">
                    <label for="commsEstudiante">Estudiante asociado</label>
                    <select name="id_estudiante" id="commsEstudiante">
                        <option value="">Todos los estudiantes del responsable</option>
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <option value="<?= (int) $estudiante['id_estudiante'] ?>"
                                    data-responsable="<?= (int) $estudiante['id_responsable'] ?>"
                                    <?= (!empty($defaultEstudiante) && $defaultEstudiante === (int) $estudiante['id_estudiante']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($estudiante['nombre_completo']) ?> — <?= htmlspecialchars($estudiante['grado'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="chat-resume" data-resume>
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

                <div class="chat-section">
                    <label for="commsCanal">Canal de contacto</label>
                    <select name="canal" id="commsCanal">
                        <option value="email" <?= $selectedCanal === 'email' ? 'selected' : '' ?>>Correo electrónico</option>
                        <option value="whatsapp" <?= $selectedCanal === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                        <option value="sms" <?= $selectedCanal === 'sms' ? 'selected' : '' ?>>SMS</option>
                        <option value="llamada" <?= $selectedCanal === 'llamada' ? 'selected' : '' ?>>Llamada telefónica</option>
                    </select>
                </div>

                <div class="chat-section">
                    <label for="commsPlantilla">Plantilla</label>
                    <select name="id_plantilla" id="commsPlantilla">
                        <option value="">Sin plantilla predefinida</option>
                        <?php foreach ($plantillas as $plantilla): ?>
                            <option value="<?= (int) $plantilla['id_plantilla'] ?>"
                                    data-canal="<?= htmlspecialchars($plantilla['canal']) ?>"
                                    <?= $selectedPlantilla === (int) $plantilla['id_plantilla'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($plantilla['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="chat-section chat-section--subject">
                    <label for="commsAsunto">Asunto (solo correo)</label>
                    <input id="commsAsunto" name="asunto" placeholder="Ej: Recordatorio de pago — {{estudiante_nombre}}">
                </div>

                <div class="chat-section">
                    <label for="commsResultado">Resultado esperado</label>
                    <input id="commsResultado" name="resultado" placeholder="Ej: Compromiso de pago confirmado">
                </div>

                <div class="chat-preview">
                    <div class="chat-preview-head">
                        <span class="label">Previsualización</span>
                        <strong data-preview-canal><?= htmlspecialchars(ucfirst($selectedCanal)) ?></strong>
                    </div>
                    <div class="chat-preview-meta">
                        <span class="label">Enviar a</span>
                        <strong data-preview-destinatario>Selecciona un responsable</strong>
                    </div>
                    <div class="chat-preview-body" data-preview>
                        <div class="placeholder">Selecciona un responsable y una plantilla para visualizar el contenido.</div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>

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
                    switch ($estado) {
                        case 'enviado':
                            $estadoLabel = ['label' => 'Enviado', 'class' => 'success'];
                            break;
                        case 'error':
                            $estadoLabel = ['label' => 'Error', 'class' => 'danger'];
                            break;
                        case 'registrado':
                            $estadoLabel = ['label' => 'Registrado', 'class' => 'neutral'];
                            break;
                        default:
                            $estadoLabel = ['label' => ucfirst($estado ?: 'Pendiente'), 'class' => 'neutral'];
                            break;
                    }
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

<script>
window.COMMS_DATA = <?= $datasetJson ?>;
</script>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
