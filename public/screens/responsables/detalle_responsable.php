<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Detalle del responsable</title>
<link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/global.css">
<link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/modules/responsables.css">
</head>
<body style="background:transparent" data-responsable="María Rodríguez" data-periodo="Agosto 2025" data-valor="$ 1.200.000" data-fecha-limite="15 de agosto" data-estudiante="Estudiante 1">
<div class="container" style="padding:18px 20px 28px">
  <div class="breadcrumbs">Cobranzas / Responsables / Detalle</div>
  <h2 class="section">Detalle del responsable</h2>

  <div class="card comm-notifications">
    <div class="comm-notifications__header">
      <div>
        <h3>Panel de comunicaciones en tiempo real</h3>
        <p>Visualiza los mensajes que llegan desde Twilio, responde rápidamente y centraliza todo el historial del responsable.</p>
      </div>
      <div class="comm-status">
        <span class="status-pill success">Twilio conectado</span>
        <span class="muted">Número activo: +57 300 111 2233 • Última sincronización hace 1 minuto</span>
      </div>
    </div>
    <div class="comm-notifications__list">
      <button class="notification is-unread" data-target="whatsappSection" type="button">
        <span class="badge-channel whatsapp">WA</span>
        <div>
          <strong>María Rodríguez</strong>
          <span>“Acabo de subir el soporte del pago”</span>
        </div>
        <time>Hace 2 min</time>
      </button>
      <button class="notification" data-target="activityTimeline" type="button">
        <span class="badge-channel email">EM</span>
        <div>
          <strong>Programación de correo</strong>
          <span>Plantilla “Estado de cuenta” lista para envío</span>
        </div>
        <time>09:15</time>
      </button>
      <button class="notification" data-target="callTimeline" type="button">
        <span class="badge-channel call">LL</span>
        <div>
          <strong>Registrar llamada</strong>
          <span>Documenta compromisos pactados por teléfono</span>
        </div>
        <time>08:40</time>
      </button>
    </div>
  </div>

  <div class="comm-layout">
    <section class="main-column">
      <div class="card chat-card" id="whatsappSection">
        <div class="chat-header">
          <div class="contact">
            <div class="avatar avatar-initial">MR</div>
            <div>
              <h3>WhatsApp con María Rodríguez</h3>
              <span>Responsable de Estudiante 1 • Gestión vía Twilio</span>
            </div>
          </div>
          <div class="actions">
            <button class="btn secondary" type="button" data-action="history">Ver historial completo</button>
            <button class="btn secondary" type="button" data-action="export">Descargar conversación</button>
          </div>
        </div>
        <div class="chat-thread" id="whatsappThread">
          <div class="chat-divider">Hoy</div>
          <div class="message message-out">
            <div class="message-bubble">
              <header>Laura Espinosa • Gestora</header>
              <p>Hola María, esperamos que estés muy bien. Te escribimos para recordarte el saldo pendiente del periodo de agosto que asciende a $1.200.000.</p>
              <p>Cuéntame si necesitas un enlace de pago o apoyo para dividir la deuda en cuotas.</p>
              <footer><span>08:34</span><span class="status">Enviado ✓✓</span></footer>
            </div>
          </div>
          <div class="message message-in" id="messageSoporte">
            <div class="message-bubble">
              <header>María Rodríguez</header>
              <p>Buenos días, ya realicé el pago esta mañana y adjunto el soporte.</p>
              <a class="message-attachment" href="<?php echo $BASE_URL; ?>assets/docs/recibo_pago_agosto.pdf" download>📄 Recibo_pago_agosto.pdf</a>
              <footer><span>08:36</span></footer>
            </div>
          </div>
          <div class="message message-out">
            <div class="message-bubble">
              <header>Laura Espinosa • Gestora</header>
              <p>Perfecto, muchas gracias por el soporte. Lo revisaré y te confirmo cuando quede aplicado en el sistema.</p>
              <footer><span>08:37</span><span class="status">Leído ✓✓</span></footer>
            </div>
          </div>
          <div class="message message-in" id="messageConfirmacion">
            <div class="message-bubble">
              <header>María Rodríguez</header>
              <p>¿Me confirmas cuándo se verá reflejado? Así informo a mi hijo.</p>
              <footer><span>08:38</span></footer>
            </div>
          </div>
          <div class="typing-indicator" id="typingIndicator" hidden>
            María está escribiendo <span>•</span><span>•</span><span>•</span>
          </div>
        </div>
        <div class="chat-composer">
          <form id="chatComposer">
            <div class="composer-row">
              <div class="grow">
                <label>Plantillas rápidas</label>
                <div class="template-chips">
                  <button class="template-chip" type="button" data-message="Hola {{responsable}}, esperamos que te encuentres bien. Te recordamos que el saldo del periodo {{periodo}} es de {{valor}}. ¿Podemos ayudarte con un enlace de pago?">Recordatorio</button>
                  <button class="template-chip" type="button" data-message="Hola {{responsable}}, confirmamos la recepción del soporte y en las próximas 24 horas verás reflejado el pago. ¡Gracias por tu pronta respuesta!">Confirmación</button>
                  <button class="template-chip" type="button" data-message="Buen día {{responsable}}. Te comparto el estado de cuenta actualizado de {{estudiante}}. Si deseas pactar un acuerdo, avísanos para acompañarte en el proceso.">Estado de cuenta</button>
                </div>
              </div>
              <div class="composer-sidebar">
                <label for="channelSelect">Canal</label>
                <select id="channelSelect">
                  <option value="whatsapp" selected>WhatsApp (Twilio)</option>
                  <option value="sms">SMS (Twilio)</option>
                  <option value="email">Email</option>
                </select>
                <label for="scheduleAt">Programar envío</label>
                <input type="datetime-local" id="scheduleAt">
              </div>
            </div>
            <div class="composer-row">
              <div class="grow">
                <label for="messageInput">Mensaje</label>
                <textarea id="messageInput" placeholder="Escribe un mensaje o aplica una plantilla..."></textarea>
              </div>
            </div>
            <div id="selectedFiles"></div>
            <div class="composer-actions">
              <label class="upload-label" for="attachmentInput">
                <input type="file" id="attachmentInput" multiple hidden>
                Adjuntar archivo
              </label>
              <button class="btn secondary" type="button" id="openTemplates">Ver biblioteca de plantillas</button>
              <button class="btn" type="submit">Enviar por Twilio</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card activity-card" id="activityTimeline">
        <h3>Actividad reciente por canal</h3>
        <div class="activity-feed">
          <div class="activity-entry">
            <span class="badge-channel whatsapp">WA</span>
            <div class="content">
              <header>
                <span>María Rodríguez respondió por WhatsApp</span>
                <time>08:36</time>
              </header>
              <p>Confirmó el abono del mes de agosto y adjuntó soporte en PDF.</p>
              <footer>Atendió Laura Espinosa</footer>
            </div>
          </div>
          <div class="activity-entry">
            <span class="badge-channel email">EM</span>
            <div class="content">
              <header>
                <span>Plantilla “Estado de cuenta” programada</span>
                <time>09:15</time>
              </header>
              <p>Se programó el envío automático para las 17:00 a través de SendGrid.</p>
              <footer>Automatización Twilio SendGrid</footer>
            </div>
          </div>
          <div class="activity-entry">
            <span class="badge-channel call">LL</span>
            <div class="content">
              <header>
                <span>Llamada registrada</span>
                <time>Ayer 17:40</time>
              </header>
              <p>Se pactó seguimiento para confirmar pago total antes del 15 de agosto.</p>
              <footer>Agente: Juan García</footer>
            </div>
          </div>
        </div>
      </div>
    </section>

    <aside class="side-column">
      <div class="card summary-card">
        <div class="profile-block">
          <div class="avatar avatar-initial">MR</div>
          <div>
            <h3 style="margin:0">María Rodríguez</h3>
            <span class="small">Responsable financiera</span>
          </div>
        </div>
        <ul>
          <li><span>Documento</span>CC 10203040</li>
          <li><span>Correo</span>maria@demo.edu</li>
          <li><span>Celular WhatsApp</span>+57 300 000 0000</li>
          <li><span>Estudiantes a cargo</span>Estudiante 1 (6°) • Estudiante 2 (3°)</li>
        </ul>
      </div>

      <div class="card quick-actions-card">
        <h3>Acciones rápidas</h3>
        <div class="toolbar">
          <button class="btn" type="button" data-scroll="#whatsappSection">WhatsApp</button>
          <button class="btn secondary" type="button" data-scroll="#activityTimeline">Email</button>
          <button class="btn secondary" type="button" data-scroll="#callTimeline">Llamada</button>
        </div>
        <div class="toolbar">
          <a class="btn" data-open="modalPago">Registrar pago</a>
          <a class="btn" data-open="modalAcuerdo">Crear acuerdo</a>
        </div>
      </div>

      <div class="card templates-card" id="templatesLibrary">
        <h3>Biblioteca de plantillas</h3>
        <p class="small">Personaliza y reutiliza tus mensajes más frecuentes por canal.</p>
        <div class="template-list">
          <div class="template-item">
            <header>Recordatorio suave <button class="template-chip" type="button" data-message="Hola {{responsable}}, esperamos que te encuentres bien. Te recordamos que el saldo del periodo {{periodo}} es de {{valor}}. Si ya realizaste el pago, por favor comparte el soporte.">Usar</button></header>
            <p>Hola {{responsable}}, esperamos que te encuentres bien. Te recordamos que el saldo del periodo {{periodo}} es de {{valor}}. Si ya realizaste el pago, por favor comparte el soporte.</p>
            <footer>WhatsApp • Actualizado 02 Ago 2025</footer>
          </div>
          <div class="template-item">
            <header>Agradecimiento <button class="template-chip" type="button" data-message="Hola {{responsable}}, confirmamos la recepción del pago de {{valor}}. Tu estado de cuenta se actualizará antes de {{fecha_limite}}. Gracias por mantenerte al día.">Usar</button></header>
            <p>Hola {{responsable}}, confirmamos la recepción del pago de {{valor}}. Tu estado de cuenta se actualizará antes de {{fecha_limite}}. Gracias por mantenerte al día.</p>
            <footer>WhatsApp y Email • Actualizado 28 Jul 2025</footer>
          </div>
          <div class="template-item">
            <header>Acuerdo de pago <button class="template-chip" type="button" data-message="Hola {{responsable}}, con gusto podemos elaborar un acuerdo para el saldo de {{valor}}. Propongo 3 cuotas iguales iniciando el {{fecha_limite}}. ¿Te funciona esta opción?">Usar</button></header>
            <p>Hola {{responsable}}, con gusto podemos elaborar un acuerdo para el saldo de {{valor}}. Propongo 3 cuotas iguales iniciando el {{fecha_limite}}. ¿Te funciona esta opción?</p>
            <footer>SMS • Actualizado 20 Jul 2025</footer>
          </div>
          <div class="template-item">
            <header>Llamada de seguimiento <button class="template-chip" type="button" data-message="Hola {{responsable}}, intentamos comunicarnos contigo para hablar del estado de cuenta de {{estudiante}}. ¿Nos confirmas cuándo podemos llamarte?">Usar</button></header>
            <p>Hola {{responsable}}, intentamos comunicarnos contigo para hablar del estado de cuenta de {{estudiante}}. ¿Nos confirmas cuándo podemos llamarte?</p>
            <footer>WhatsApp y SMS • Actualizado 12 Jul 2025</footer>
          </div>
        </div>
      </div>

      <div class="card call-card" id="callTimeline">
        <h3>Registro manual de llamadas</h3>
        <form id="callForm">
          <div class="inline">
            <div>
              <label for="callDate">Fecha</label>
              <input type="date" id="callDate" value="2025-08-12">
            </div>
            <div>
              <label for="callTime">Hora</label>
              <input type="time" id="callTime" value="09:45">
            </div>
            <div>
              <label for="callDuration">Duración (min)</label>
              <input type="number" id="callDuration" min="1" value="5">
            </div>
          </div>
          <div>
            <label for="callNotes">Resumen</label>
            <textarea id="callNotes" placeholder="Anota compromisos, acuerdos y próximos pasos."></textarea>
          </div>
          <div class="form-actions">
            <button class="btn secondary" type="reset">Limpiar</button>
            <button class="btn" type="submit">Guardar llamada</button>
          </div>
        </form>
        <div class="calls-timeline" id="callLog">
          <div class="call-event">
            <strong>12 ago 2025 • 09:10 a. m.</strong>
            <span>Duración: 04:12 min • Registró Laura Espinosa</span>
            <p>Se acordó enviar soporte del pago realizado y confirmar el saldo restante.</p>
          </div>
          <div class="call-event">
            <strong>08 ago 2025 • 16:30 p. m.</strong>
            <span>Duración: 03:05 min • Registró Juan García</span>
            <p>Se recordó la fecha límite del 15 de agosto y se ofreció acuerdo de pago.</p>
          </div>
        </div>
      </div>

      <div class="card attachments-card">
        <h3>Adjuntos compartidos</h3>
        <ul>
          <li>
            <div>
              <strong>Recibo_pago_agosto.pdf</strong>
              <span>WhatsApp • 12 ago 2025 - 08:36</span>
            </div>
            <a href="<?php echo $BASE_URL; ?>assets/docs/recibo_pago_agosto.pdf" download>Descargar</a>
          </li>
          <li>
            <div>
              <strong>Estado_de_cuenta_julio.xlsx</strong>
              <span>Email • 05 ago 2025 - 10:12</span>
            </div>
            <a href="<?php echo $BASE_URL; ?>assets/docs/estado_de_cuenta_julio.xlsx" download>Descargar</a>
          </li>
          <li>
            <div>
              <strong>Audio_compromiso.m4a</strong>
              <span>Llamada • 01 ago 2025 - 15:45</span>
            </div>
            <a href="<?php echo $BASE_URL; ?>assets/docs/audio_compromiso.m4a" download>Descargar</a>
          </li>
        </ul>
      </div>
    </aside>
  </div>
</div>

<div class="modal-backdrop" id="modalPago">
  <div class="modal" role="dialog" aria-modal="true">
    <header>Registrar pago <button class="close-x" data-close>&times;</button></header>
    <div class="content">
      <div class="two">
        <div><label>Fecha de pago</label><input type="date" value="2025-08-12"></div>
        <div><label>Valor total</label><input value="200000"></div>
        <div><label>Soporte</label><input type="file"></div>
        <div><label>Observaciones</label><input></div>
      </div>
    </div>
    <div class="actions">
      <button class="btn secondary" data-close>Cancelar</button>
      <button class="btn" data-close>Guardar (demo)</button>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="modalAcuerdo">
  <div class="modal" role="dialog" aria-modal="true">
    <header>Crear acuerdo de pago <button class="close-x" data-close>&times;</button></header>
    <div class="content">
      <div class="two">
        <div><label>Monto del acuerdo</label><input value="500000"></div>
        <div><label>Fecha primera cuota</label><input type="date" value="2025-09-01"></div>
        <div><label>Número de cuotas</label><input value="3"></div>
        <div><label>Observaciones</label><input></div>
      </div>
    </div>
    <div class="actions">
      <button class="btn secondary" data-close>Cancelar</button>
      <button class="btn" data-close>Guardar (demo)</button>
    </div>
  </div>
</div>

<script src="<?php echo $BASE_URL; ?>assets/js/modules/modals.js"></script>
<script src="<?php echo $BASE_URL; ?>assets/js/modules/chat-demo.js"></script>
</body>
</html>
