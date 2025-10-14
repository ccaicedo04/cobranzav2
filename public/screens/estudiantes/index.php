<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Estudiantes</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>

<div class="breadcrumbs">Cobranzas / Estudiantes</div><h2 class="section">Estudiantes</h2>
<div class="card">
  <div class="toolbar">
    <input style='max-width:280px' placeholder="Buscar...">
    <select><option>Todos</option></select>
    <select><option>Filtrar por sede</option></select>
    <span style="flex:1"></span><a class="btn">Nuevo</a>
  </div>
  <table class="table"><thead><tr><th>Nombre</th><th>Documento</th><th>Código</th><th>Responsable</th><th>Sede</th><th>Acciones</th></tr></thead><tbody><tr><td>Estudiante 1</td><td>E001</td><td>COD1</td><td>María Rodríguez</td><td>Sede Norte</td><td><a class='btn' href='#' onclick="parent.location.hash='route=screens/estudiantes/detalle_estudiante.php'">Ver</a></td></tr><tr><td>Estudiante 2</td><td>E002</td><td>COD2</td><td>María Rodríguez</td><td>Sede Norte</td><td><a class='btn' href='#' onclick="parent.location.hash='route=screens/estudiantes/detalle_estudiante.php'">Ver</a></td></tr></tbody></table>
</div>

</div></body></html>