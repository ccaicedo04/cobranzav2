<?php
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$pos = strpos($script, '/public/');
$BASE_URL = ($pos !== false) ? substr($script, 0, $pos + 8) : '/';
?>
<div class="appbar">
  <div class="container">
    <img src="<?php echo $BASE_URL; ?>images/logo-yoyjo.png" href="#" data-route="screens/dashboard.php" alt="Logo" style="height:40px;width:auto"/>
    <div class="tenant">
      <span class="label" style="margin-right:4px">Colegio / Sede</span>
      <select><option>Colegio Central</option><option>Colegio Demo</option></select>
      <select><option>Sede Centro</option><option>Sede Norte</option></select>
    </div>
    <nav class="nav">
      <div class="group">
        <button>Cobranzas ▾</button>
        <div class="dropdown">
          <a href="#" data-route="screens/responsables/index.php">Responsables</a>
          <a href="#" data-route="screens/estudiantes/index.php">Estudiantes</a>
          <a href="#" data-route="screens/deudas/index.php">Deudas</a>
          <a href="#" data-route="screens/comunicaciones/index.php">Comunicaciones</a>
          <a href="#" data-route="screens/carga_masiva/index.php">Carga masiva</a>
        </div>
      </div>
      <div class="group">
        <button>Administración ▾</button>
        <div class="dropdown">
          <a href="#" data-route="screens/admin/colegios.php">Colegios</a>
          <a href="#" data-route="screens/admin/sedes.php">Sedes</a>
          <a href="#" data-route="screens/admin/usuarios.php">Usuarios</a>
          <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:6px 0">
          <a href="#" data-route="screens/reportes/index.php">Reportes</a>
          <a href="#" data-route="screens/auditoria/index.php">Auditoría</a>
        </div>
      </div>
      <div class="group">
        <button>Parametrización ▾</button>
        <div class="dropdown">
          <a href="#" data-route="screens/parametrizacion/conceptos.php">Conceptos de deuda</a>
          <a href="#" data-route="screens/parametrizacion/periodos.php">Períodos</a>
          <a href="#" data-route="screens/parametrizacion/configuracion.php">Configuración del colegio</a>
          <a href="#" data-route="screens/parametrizacion/plantillas.php">Plantillas de comunicación</a>
          <a href="#" data-route="screens/parametrizacion/parametros.php">Parámetros</a>
        </div>
      </div>
    </nav>
    <div class="userbox">
      <button id="userBtn">Administrador Global ▾</button>
      <div class="menu" id="userMenu">
        <a href="#" data-route="screens/perfil.php">Ver perfil</a>
        <a id="logoutLink" href="login.php">Cerrar sesión</a>
      </div>
    </div>
  </div>
</div>
