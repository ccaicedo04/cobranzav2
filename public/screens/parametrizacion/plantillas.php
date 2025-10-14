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
  <table class='table'><thead><tr><th>Nombre</th><th>Canal</th><th>Última actualización</th><th>Acciones</th></tr></thead><tbody><tr><td>Recordatorio de pago</td><td>WhatsApp</td><td>2025-08-01</td><td>Editar</td></tr><tr><td>Estado de cuenta</td><td>Email</td><td>2025-08-02</td><td>Editar</td></tr></tbody></table>
</div>
</div></body></html>