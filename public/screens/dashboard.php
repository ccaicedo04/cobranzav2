<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Dashboard</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>

<div class="breadcrumbs">Inicio / Dashboard</div><h2 class="section">Dashboard ejecutivo</h2>

<div class="card hero-card">
  <div>
    <p class="small">COBRANZA • Panel de control</p>
    <h3>Visión global de cartera y recaudo</h3>
    <p class="small">Consolida la información financiera en tiempo real, con trazabilidad total por colegio y sede.</p>
  </div>
  <div class="hero-actions">
    <button class="btn">Registrar pago</button>
    <button class="btn secondary">Ver reportes</button>
  </div>
</div>

<div class="card filter-card">
  <div class="filters">
    <div>
      <label>Colegio</label>
      <select>
        <option>Colegio Central</option>
        <option>Colegio Los Pinos</option>
      </select>
    </div>
    <div>
      <label>Sede</label>
      <select>
        <option>Todas las sedes</option>
        <option>Principal</option>
        <option>Occidente</option>
      </select>
    </div>
    <div>
      <label>Periodo</label>
      <select>
        <option>Últimos 6 meses</option>
        <option>Últimos 12 meses</option>
        <option>Año actual</option>
      </select>
    </div>
    <div class="filter-actions">
      <button class="btn secondary">Aplicar filtros</button>
    </div>
  </div>
</div>

<div class="grid grid-4 kpi-grid">
  <div class="card kpi-card">
    <div class="kpi">Cartera activa</div>
    <strong>$ 1.240.000</strong>
    <span class="kpi-note positive">+8% vs mes anterior</span>
  </div>
  <div class="card kpi-card">
    <div class="kpi">Recaudo del mes</div>
    <strong>$ 820.000</strong>
    <span class="kpi-note positive">+12% cumplimiento</span>
  </div>
  <div class="card kpi-card">
    <div class="kpi">% Morosidad</div>
    <strong>18,4%</strong>
    <span class="kpi-note warning">-2,3 pp vs mes anterior</span>
  </div>
  <div class="card kpi-card">
    <div class="kpi">Cartera vencida crítica</div>
    <strong>$ 160.000</strong>
    <span class="kpi-note danger">48 responsables en riesgo</span>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3>Alertas estratégicas</h3>
    <div class="alert-list">
      <div class="alert-item">
        <span class="badge danger">Crítico</span>
        <div>
          <strong>12 responsables superan 90 días</strong>
          <p class="small">Se recomienda activar plan de cobranza intensiva.</p>
        </div>
      </div>
      <div class="alert-item">
        <span class="badge warning">Atención</span>
        <div>
          <strong>Morosidad sube en sede Occidente</strong>
          <p class="small">+4% respecto al promedio general.</p>
        </div>
      </div>
      <div class="alert-item">
        <span class="badge info">Info</span>
        <div>
          <strong>25 pagos pendientes de conciliación</strong>
          <p class="small">Revisar registros manuales en las últimas 48 horas.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <h3>Gestiones rápidas</h3>
    <div class="quick-actions">
      <button class="btn">Enviar WhatsApp masivo</button>
      <button class="btn secondary">Crear plantilla</button>
      <button class="btn secondary">Programar recordatorios</button>
      <button class="btn secondary">Ver auditoría</button>
    </div>
    <div class="progress-card">
      <div>
        <p class="small">Cumplimiento de meta mensual</p>
        <strong>78%</strong>
      </div>
      <div class="progress-track"><span style="width:78%"></span></div>
      <p class="small">Meta: $ 1.050.000</p>
    </div>
  </div>
</div>

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
      <tr><td>2025-08-01</td><td>María Rodríguez</td><td>WhatsApp</td><td>Recordatorio de pago de pensión julio.</td><td><a class="btn">WhatsApp</a></td></tr>
      <tr><td>2025-07-30</td><td>Luisa Gómez</td><td>SMS</td><td>Notificación de mora 30 días.</td><td><a class="btn">SMS</a></td></tr>
      <tr><td>2025-07-28</td><td>Juan García</td><td>Email</td><td>Estado de cuenta y plan de pago.</td><td><a class="btn">Email</a></td></tr>
    </tbody>
  </table>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?php echo $BASE_URL; ?>assets/js/modules/charts-dashboard.js"></script>
</body></html>
