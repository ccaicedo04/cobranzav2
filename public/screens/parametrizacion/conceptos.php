<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Conceptos de deuda</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Conceptos de deuda</div><h2 class='section'>Conceptos de deuda</h2>

<div class='card'>
  <div class='toolbar'>
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <button class='btn secondary'>Filtrar</button>
    <span style='flex:1'></span><a class='btn'>Nuevo</a>
  </div>
  <table class='table'><thead><tr><th>Código</th><th>Nombre</th><th>Frecuencia</th><th>Activo</th></tr></thead><tbody><tr><td>PEN</td><td>Pensión</td><td>mensual</td><td>sí</td></tr><tr><td>TRA</td><td>Transporte</td><td>mensual</td><td>sí</td></tr></tbody></table>
</div>
</div></body></html>