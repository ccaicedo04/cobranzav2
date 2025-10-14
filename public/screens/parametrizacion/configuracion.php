<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Configuración del colegio</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Configuración del colegio</div><h2 class='section'>Configuración del colegio</h2>

<div class='card'>
  <div class='two'>
    <div><label>Nombre del colegio</label><input value='Colegio Central'></div>
    <div><label>NIT</label><input value='800000001'></div>
    <div><label>Dirección</label><input value='Calle 123 #45-67'></div>
    <div><label>Teléfono</label><input value='(1) 555 0101'></div>
  </div>
  <div class='toolbar' style='margin-top:12px'><a class='btn'>Guardar</a></div>
</div>
</div></body></html>