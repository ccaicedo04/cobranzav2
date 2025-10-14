<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Parámetros</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Parámetros</div><h2 class='section'>Parámetros</h2>

<div class='card'>
  <div class='two'>
    <div><label>Moneda</label><input value='COP'></div>
    <div><label>Separador de miles</label><input value='.'></div>
    <div><label>Separador decimal</label><input value=','></div>
    <div><label>Formato de fecha</label><input value='YYYY-MM-DD'></div>
  </div>
  <div class='toolbar' style='margin-top:12px'><a class='btn'>Guardar</a></div>
</div>
</div></body></html>