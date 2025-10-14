<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Usuarios</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'>
<div class='container' style='padding:18px 20px 8px'>
<div class='breadcrumbs'>Usuarios</div><h2 class='section'>Usuarios</h2>

<div class='card'>
  <div class='toolbar'>
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <button class='btn secondary'>Filtrar</button>
    <span style='flex:1'></span><a class='btn'>Nuevo</a>
  </div>
  <table class='table'><thead><tr><th>Usuario</th><th>Rol</th><th>Colegio</th><th>Sede</th><th>Estado</th></tr></thead><tbody><tr><td>admin</td><td>admin_global</td><td>-</td><td>-</td><td>activo</td></tr><tr><td>admin1</td><td>admin_colegio</td><td>Colegio Central</td><td>Todas</td><td>activo</td></tr><tr><td>agente1</td><td>agente</td><td>Colegio Central</td><td>Sede Centro</td><td>activo</td></tr></tbody></table>
</div>
</div></body></html>