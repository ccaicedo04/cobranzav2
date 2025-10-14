<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Sedes</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Sedes</div><h2 class='section'>Sedes</h2>

<div class='card'>
  <div class='toolbar'>
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <button class='btn secondary'>Filtrar</button>
    <span style='flex:1'></span><a class='btn'>Nuevo</a>
  </div>
  <table class='table'><thead><tr><th>ID</th><th>Colegio</th><th>Nombre</th><th>Teléfono</th></tr></thead><tbody><tr><td>1</td><td>Colegio Central</td><td>Sede Centro</td><td>(1) 555 0101</td></tr><tr><td>2</td><td>Colegio Central</td><td>Sede Norte</td><td>(1) 555 0102</td></tr></tbody></table>
</div>
</div></body></html>