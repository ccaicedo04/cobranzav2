<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Detalle del estudiante</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>

<div class="breadcrumbs">Cobranzas / Estudiantes / Detalle</div><h2 class="section">Detalle del estudiante</h2>
<div class="grid grid-3">
  <div class="card">
    <h3>Información del estudiante</h3>
    <div class="grid grid-2">
      <div><div class="small">Nombre</div><strong>Estudiante 1</strong></div>
      <div><div class="small">Documento</div><strong>E001</strong></div>
      <div><div class="small">Código</div><strong>COD1</strong></div>
      <div><div class="small">Fecha de nacimiento</div><strong>2013-02-10</strong></div>
    </div>
  </div>
  <div class="card">
    <h3>Responsable financiero</h3>
    <div><strong>María Rodríguez</strong></div>
    <div class="small">Doc. 10203040 • 3000000000 • maria@demo.edu</div>
    <div class="toolbar" style="margin-top:8px"><a class="btn" href="#" onclick="parent.location.hash='route=screens/responsables/detalle_responsable.php'">Ver responsable</a><a class="btn">WhatsApp</a></div>
  </div>
  <div class="card">
    <h3>KPIs</h3>
    <div class="grid grid-2">
      <div><div class="kpi">Saldo total</div><strong>$ 900.000</strong></div>
      <div><div class="kpi">Pagos últimos 6 meses</div><strong>$ 320.000</strong></div>
    </div>
  </div>
</div>
<div class="card" style="margin-top:16px">
  <h3>Deudas</h3>
  <table class="table">
    <thead><tr><th>Periodo</th><th>Concepto</th><th>Monto</th><th>Saldo</th><th>Estado</th></tr></thead>
    <tbody><tr><td>2025-07</td><td>Pensión</td><td>$ 400.000</td><td>$ 200.000</td><td><span class="badge">parcial</span></td></tr>
           <tr><td>2025-07</td><td>Transporte</td><td>$ 100.000</td><td>$ 100.000</td><td><span class="badge">pendiente</span></td></tr>
    </tbody>
  </table>
</div>
<div class="grid grid-2" style="margin-top:16px">
  <div class="card">
    <h3>Pagos</h3>
    <table class="table">
      <thead><tr><th>Fecha</th><th>Valor</th><th>Soporte</th></tr></thead>
      <tbody><tr><td>2025-07-02</td><td>$ 200.000</td><td>-</td></tr></tbody>
    </table>
  </div>
  <div class="card">
    <h3>Comunicaciones</h3>
    <table class="table">
      <thead><tr><th>Fecha</th><th>Canal</th><th>Contenido</th></tr></thead>
      <tbody><tr><td>2025-07-30</td><td>WhatsApp</td><td>Recordatorio</td></tr></tbody>
    </table>
  </div>
</div>

</div></body></html>