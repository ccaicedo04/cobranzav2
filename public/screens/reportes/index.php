<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Reportes</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Reportes</div><h2 class='section'>Reportes</h2>

<div class='card'>
  <div class='toolbar'>
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <button class='btn secondary'>Filtrar</button>
    <span style='flex:1'></span><a class='btn'>Nuevo</a>
  </div>
  <table class='table'><thead><tr><th>Reporte</th><th>Descripción</th><th>Acción</th></tr></thead><tbody><tr><td>Cartera por responsable</td><td>Saldo total por responsable</td><td>Ver</td></tr><tr><td>Recaudo últimos 6 meses</td><td>Tendencia de pagos recientes</td><td>Ver</td></tr></tbody></table>
</div>
</div></body></html>