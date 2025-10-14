<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Períodos</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Períodos</div><h2 class='section'>Períodos</h2>

<div class='card'>
  <div class='toolbar'>
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <button class='btn secondary'>Filtrar</button>
    <span style='flex:1'></span><a class='btn'>Nuevo</a>
  </div>
  <table class='table'><thead><tr><th>Código</th><th>Nombre</th><th>Inicio</th><th>Fin</th></tr></thead><tbody><tr><td>2025-07</td><td>Julio 2025</td><td>2025-07-01</td><td>2025-07-31</td></tr><tr><td>2025-08</td><td>Agosto 2025</td><td>2025-08-01</td><td>2025-08-31</td></tr></tbody></table>
</div>
</div></body></html>