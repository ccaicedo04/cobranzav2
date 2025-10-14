<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Deudas</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Deudas</div><h2 class='section'>Deudas</h2>

<div class='card'>
  <div class='toolbar'>
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <button class='btn secondary'>Filtrar</button>
    <span style='flex:1'></span><a class='btn'>Nuevo</a>
  </div>
  <table class='table'><thead><tr><th>Estudiante</th><th>Periodo</th><th>Conceptos</th><th>Monto</th><th>Saldo</th><th>Estado</th></tr></thead><tbody><tr><td>Estudiante 1</td><td>2025-07</td><td>Pensión</td><td>$ 400.000</td><td>$ 200.000</td><td>parcial</td></tr><tr><td>Estudiante 1</td><td>2025-07</td><td>Transporte</td><td>$ 100.000</td><td>$ 100.000</td><td>pendiente</td></tr><tr><td>Estudiante 2</td><td>2025-07</td><td>Pensión</td><td>$ 300.000</td><td>$ 100.000</td><td>parcial</td></tr></tbody></table>
</div>
</div></body></html>