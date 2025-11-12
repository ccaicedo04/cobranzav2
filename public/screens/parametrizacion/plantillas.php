<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Plantillas de comunicación</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Plantillas de comunicación</div><h2 class='section'>Plantillas de comunicación</h2>

<div class='card'>
  <div class='toolbar'>
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <button class='btn secondary'>Filtrar</button>
    <span style='flex:1'></span><a class='btn'>Nuevo</a>
  </div>
  <table class='table'>
    <thead><tr><th>Nombre</th><th>Canal</th><th>Última actualización</th><th>Propietario</th><th>Acciones</th></tr></thead>
    <tbody>
      <tr><td>Recordatorio de pago</td><td>WhatsApp</td><td>2025-08-01</td><td>Equipo de cartera</td><td>Editar</td></tr>
      <tr><td>Estado de cuenta</td><td>Email</td><td>2025-08-02</td><td>Automatización</td><td>Editar</td></tr>
      <tr><td>Agradecimiento por pago</td><td>WhatsApp / Email</td><td>2025-07-28</td><td>Laura Espinosa</td><td>Editar</td></tr>
      <tr><td>Acuerdo de pago flexible</td><td>SMS</td><td>2025-07-24</td><td>Juan García</td><td>Editar</td></tr>
      <tr><td>Aviso preventivo de mora</td><td>WhatsApp / SMS</td><td>2025-07-20</td><td>Equipo de cartera</td><td>Editar</td></tr>
      <tr><td>Confirmación de llamada</td><td>Email</td><td>2025-07-15</td><td>Automatización</td><td>Editar</td></tr>
    </tbody>
  </table>
</div>

<div class='grid grid-2' style='margin-top:18px'>
  <div class='card' style='padding:22px'>
    <h3 style='margin-top:0'>Plantilla: Recordatorio de pago</h3>
    <p style='color:#4b5563;line-height:1.6'>Hola {{responsable}}, te saludamos desde el área de cartera del colegio. Te recordamos que el saldo del periodo {{periodo}} es de {{valor}}. Si ya realizaste el pago por favor comparte el soporte, de lo contrario podemos ayudarte con un acuerdo.</p>
    <div class='toolbar' style='margin-top:18px'>
      <span class='badge'>WhatsApp</span>
      <span class='badge'>Twilio</span>
      <span style='flex:1'></span>
      <a class='btn secondary'>Duplicar</a>
      <a class='btn'>Usar en conversación</a>
    </div>
  </div>
  <div class='card' style='padding:22px'>
    <h3 style='margin-top:0'>Plantilla: Aviso preventivo de mora</h3>
    <p style='color:#4b5563;line-height:1.6'>Hola {{responsable}}, identificamos que el saldo del periodo {{periodo}} continúa pendiente y el vencimiento es el {{fecha_limite}}. Queremos apoyarte para evitar intereses. ¿Deseas recibir opciones de financiación o reagendar una llamada?</p>
    <div class='toolbar' style='margin-top:18px'>
      <span class='badge'>SMS</span>
      <span class='badge'>WhatsApp</span>
      <span style='flex:1'></span>
      <a class='btn secondary'>Compartir</a>
      <a class='btn'>Editar</a>
    </div>
  </div>
</div>
</div></body></html>

