<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Carga masiva</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>
<div class="breadcrumbs">Cobranzas / Carga masiva</div>
<h2 class="section">Carga masiva</h2>
<div class="grid grid-2">
  <div class="card">
    <h3>Subir archivo</h3>
    <label>Tipo de carga</label>
    <select><option>deudas</option><option>estudiantes</option></select>
    <div style="height:8px"></div>
    <label>Archivo CSV</label>
    <input type="file" accept=".csv">
    <div class="toolbar" style="margin-top:10px">
      <button class="btn" data-demo-save>Procesar (demo)</button>
      <a class="btn secondary">Descargar plantilla CSV</a>
    </div>
    <p class="small" style="margin-top:8px">
      Requisitos: UTF-8, separador coma (,), cabecera obligatoria. Campos mínimos para
      <strong>deudas</strong>: documento_responsable, documento_estudiante, concepto, periodo, monto.<br>
      Para <strong>estudiantes</strong>: documento_estudiante, nombre_completo, documento_responsable, codigo, curso.
    </p>
  </div>
  <div class="card">
    <h3>Historial de cargas</h3>
    <table class="table">
      <thead><tr><th>Fecha</th><th>Tipo</th><th>Usuario</th><th>Archivo</th><th>Estado</th><th>Detalle</th></tr></thead>
      <tbody>
        <tr><td>2025-08-01</td><td>deudas</td><td>admin</td><td>carga_deudas_julio.csv</td><td>procesado</td><td>0 errores</td></tr>
        <tr><td>2025-07-20</td><td>estudiantes</td><td>admin</td><td>estudiantes.csv</td><td>error</td><td>5 errores</td></tr>
      </tbody>
    </table>
  </div>
</div>

  <table class="table"><thead><tr><th>Fecha</th><th>Tipo</th><th>Usuario</th><th>Archivo</th><th>Estado</th><th>Detalle</th></tr></thead><tbody><tr><td>2025-08-01</td><td>deudas</td><td>admin</td><td>carga_deudas_julio.csv</td><td>procesado</td><td>0 errores</td></tr><tr><td>2025-07-20</td><td>estudiantes</td><td>admin</td><td>estudiantes.csv</td><td>error</td><td>5 errores</td></tr>
      <tr><td colspan="999" style="background:#f9fafb;padding:10px 14px">
        <span class="small">Acciones de demo activas: <strong>Nuevo</strong>, <strong>Editar</strong> y <strong>Eliminar</strong> (con confirmación).</span>
      </td></tr>
    </tbody></table>
</div>
<div class="modal-backdrop" id="modalNuevo">
  <div class="modal">
    <header>Nuevo (demo)</header>
    <div class="content">
      <div class="input-row">
        <div><label>Campo 1</label><input></div>
        <div><label>Campo 2</label><input></div>
        <div><label>Campo 3</label><input></div>
        <div><label>Campo 4</label><input></div>
      </div>
    </div>
    <footer><button class="btn secondary" data-close-modal>Cancelar</button><button class="btn" data-demo-save>Guardar</button></footer>
  
</div>
<div class="modal-backdrop" id="modalEditar">
  <div class="modal">
    <header>Editar (demo)</header>
    <div class="content">
      <div class="input-row">
        <div><label>Campo 1</label><input value="Valor A"></div>
        <div><label>Campo 2</label><input value="Valor B"></div>
        <div><label>Campo 3</label><input value="Valor C"></div>
        <div><label>Campo 4</label><input value="Valor D"></div>
      </div>
    </div>
    <footer><button class="btn secondary" data-close-modal>Cancelar</button><button class="btn" data-demo-save>Guardar</button></footer>
  </div>
</div>
<div class="toast"></div>

</div><script src='<?php echo $BASE_URL; ?>assets/js/app-ui.js'></script>
</body></html>