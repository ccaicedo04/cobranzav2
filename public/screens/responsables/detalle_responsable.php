<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Detalle del responsable</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>

<div class="breadcrumbs">Cobranzas / Responsables / Detalle</div><h2 class="section">Detalle del responsable</h2>
<div class="grid grid-3">
  <div class="card">
    <h3>Responsable financiero</h3>
    <div class="grid grid-2">
      <div><div class="small">Nombre</div><strong>María Rodríguez</strong></div>
      <div><div class="small">Documento</div><strong>10203040</strong></div>
      <div><div class="small">Correo</div><strong>maria@demo.edu</strong></div>
      <div><div class="small">Celular</div><strong>3000000000</strong></div>
    </div>
  </div>
  <div class="card">
    <h3>KPIs</h3>
    <div class="grid grid-2">
      <div><div class="kpi">Total deuda</div><strong>$ 1.200.000</strong></div>
      <div><div class="kpi">Pagos últimos 30 días</div><strong>$ 200.000</strong></div>
    </div>
  </div>
  <div class="card">
    <h3>Acciones rápidas</h3>
    <div class="toolbar"><a class="btn">WhatsApp</a><a class="btn">Email</a><a class="btn">Llamada</a><span style='flex:1'></span><a class='btn' data-open='modalPago'>Registrar pago</a><a class='btn' data-open='modalAcuerdo'>Crear acuerdo</a></div>
  </div>
</div>
<div class="grid grid-2" style="margin-top:16px">
  <div class="card">
    <h3>Estudiantes a cargo</h3>
    <table class="table">
      <thead><tr><th>Nombre</th><th>Documento</th><th>Curso</th><th>Deuda</th></tr></thead>
      <tbody><tr><td>Estudiante 1</td><td>E001</td><td>6°</td><td>$ 800.000</td></tr>
             <tr><td>Estudiante 2</td><td>E002</td><td>3°</td><td>$ 400.000</td></tr></tbody>
    </table>
  </div>
  <div class="card">
    <h3>Comunicaciones recientes</h3>
    <table class="table">
      <thead><tr><th>Fecha</th><th>Canal</th><th>Contenido</th></tr></thead>
      <tbody><tr><td>2025-08-02</td><td>WhatsApp</td><td>Recordatorio de saldo</td></tr>
             <tr><td>2025-07-30</td><td>Email</td><td>Estado de cuenta</td></tr></tbody>
    </table>
  </div>
</div>
<div class="card" style="margin-top:16px">
  <h3>Deudas del responsable</h3>
  <table class="table">
    <thead><tr><th>Estudiante</th><th>Periodo</th><th>Conceptos</th><th>Monto</th><th>Saldo</th><th>Estado</th></tr></thead>
    <tbody><tr><td>Estudiante 1</td><td>2025-07</td><td>Pensión, Transporte</td><td>$ 500.000</td><td>$ 300.000</td><td><span class="badge">pendiente</span></td></tr>
           <tr><td>Estudiante 2</td><td>2025-07</td><td>Pensión</td><td>$ 300.000</td><td>$ 100.000</td><td><span class="badge">parcial</span></td></tr>
    </tbody>
  </table>
</div>


<!-- Modales demo -->
<div class="modal-backdrop" id="modalPago">
  <div class="modal" role="dialog" aria-modal="true">
    <header>Registrar pago <button class="close-x" data-close>&times;</button></header>
    <div class="content">
      <div class="two">
        <div><label>Fecha de pago</label><input type="date" value="2025-08-12"></div>
        <div><label>Valor total</label><input value="200000"></div>
        <div><label>Soporte</label><input type="file"></div>
        <div><label>Observaciones</label><input></div>
      </div>
    </div>
    <div class="actions">
      <button class="btn secondary" data-close>Cancelar</button>
      <button class="btn" data-close>Guardar (demo)</button>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="modalAcuerdo">
  <div class="modal" role="dialog" aria-modal="true">
    <header>Crear acuerdo de pago <button class="close-x" data-close>&times;</button></header>
    <div class="content">
      <div class="two">
        <div><label>Monto del acuerdo</label><input value="500000"></div>
        <div><label>Fecha primera cuota</label><input type="date" value="2025-09-01"></div>
        <div><label>Número de cuotas</label><input value="3"></div>
        <div><label>Observaciones</label><input></div>
      </div>
    </div>
    <div class="actions">
      <button class="btn secondary" data-close>Cancelar</button>
      <button class="btn" data-close>Guardar (demo)</button>
    </div>
  </div>
</div>
<script src="<?php echo $BASE_URL; ?>assets/js/modules/modals.js"></script>
</div></body></html>
