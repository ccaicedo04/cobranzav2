<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Responsables</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>

<div class="breadcrumbs">Cobranzas / Responsables</div><h2 class="section">Responsables</h2>
<div class="card">
  <div class="toolbar">
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <select><option>Filtrar por sede</option></select>
    <span style="flex:1"></span><a class="btn">Nuevo</a>
  </div>
  <table class="table"><thead><tr><th>Nombre</th><th>Documento</th><th>Correo</th><th>Celular</th><th>Deuda total</th><th>Acciones</th></tr></thead><tbody><tr><td>María Rodríguez</td><td>10203040</td><td>maria@demo.edu</td><td>3000000000</td><td>$ 1.200.000</td><td><a class='btn' href='#' onclick="parent.location.hash='route=screens/responsables/detalle_responsable.php'">Ver</a></td></tr><tr><td>Juan García</td><td>11223344</td><td>juan@demo.edu</td><td>3000000001</td><td>$ 950.000</td><td><a class='btn' href='#' onclick="parent.location.hash='route=screens/responsables/detalle_responsable.php'">Ver</a></td></tr></tbody></table>
</div>

</div></body></html>