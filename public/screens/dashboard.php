<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Dashboard</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>

<div class="breadcrumbs">Inicio / Dashboard</div><h2 class="section">Dashboard</h2>
<div class="grid grid-3"><div class="card"><div class="kpi">Cartera Pendiente</div><strong>$ 400.000</strong></div></div>
<div class="grid grid-3">
  <div class="card chart-card"><h3>Top 5 responsables con mayor deuda</h3><canvas id="ch1" height="180"></canvas></div>
  <div class="card chart-card"><h3>Cartera reportada en los últimos 6 meses</h3><canvas id="ch2" height="180"></canvas></div>
  <div class="card chart-card"><h3>Tendencia de recaudo últimos 6 meses</h3><canvas id="ch3" height="180"></canvas></div>
</div>
<div class="card"><h3>Últimas cobranzas</h3>
  <div class="toolbar">
    <input style="max-width:260px" placeholder="Buscar por responsable o concepto">
    <select><option>Todos los canales</option><option>WhatsApp</option><option>Email</option><option>SMS</option><option>Llamada</option></select>
    <button class="btn secondary">Filtrar</button>
  </div>
  <table class="table">
    <thead><tr><th>Fecha</th><th>Responsable</th><th>Canal</th><th>Contenido</th><th>Acciones</th></tr></thead>
    <tbody>
      <tr><td>2025-08-01</td><td>María Rodríguez</td><td>WhatsApp</td><td>Recordatorio de pago...</td><td><a class="btn">WhatsApp</a></td></tr>
      <tr><td>2025-07-28</td><td>Juan García</td><td>Email</td><td>Estado de cuenta...</td><td><a class="btn">Email</a></td></tr>
    </tbody>
  </table>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?php echo $BASE_URL; ?>assets/js/modules/charts-dashboard.js"></script>
</body></html>