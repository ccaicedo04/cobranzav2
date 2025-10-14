<?php
$script = $_SERVER['SCRIPT_NAME'];
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?><!doctype html><html lang='es'><head>
<meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Perfil de usuario</title>
<link rel='stylesheet' href='<?php echo $BASE_URL; ?>assets/css/global.css'>
</head><body style='background:transparent'><div class='container' style='padding:18px 20px 8px'>
<div class="breadcrumbs">Usuario / Perfil</div><h2 class="section">Mi perfil</h2>
<div class="grid grid-3">
  <div class="card">
    <h3>Información</h3>
    <div class="grid grid-2">
      <div><div class="small">Nombre</div><strong>Administrador Global</strong></div>
      <div><div class="small">Usuario</div><strong>admin</strong></div>
      <div><div class="small">Correo</div><strong>admin@demo.edu</strong></div>
      <div><div class="small">Rol</div><span class="badge">admin_global</span></div>
    </div>
  </div>
  <div class="card">
    <h3>Preferencias</h3>
    <label>Tema</label><select><option>Claro</option><option>Oscuro</option></select>
    <div style="height:10px"></div>
    <label>Idioma</label><select><option>Español</option><option>Inglés</option></select>
    <div style="height:10px"></div>
    <button class="btn" data-demo-save>Guardar</button>
  </div>
  <div class="card">
    <h3>Seguridad</h3>
    <label>Contraseña actual</label><input type="password" value="">
    <div style="height:10px"></div>
    <label>Nueva contraseña</label><input type="password" value="">
    <div style="height:10px"></div>
    <label>Confirmar nueva contraseña</label><input type="password" value="">
    <div style="height:10px"></div>
    <button class="btn" data-demo-save>Cambiar contraseña</button>
  </div>
</div>
<div class="toast"></div>
</div><script src='<?php echo $BASE_URL; ?>assets/js/app-ui.js'></script>
</body></html>
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
