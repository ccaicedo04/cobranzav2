<?php ?><!doctype html><html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title><link rel="stylesheet" href="assets/css/global.css"></head>
<body class="login-page">
  <div class="login-wrap">
    <div class="login-left">
      <br><br><br><br>
      <h2 class="login-title">INICIAR SESIÓN</h2>
      <form onsubmit="event.preventDefault(); location.href='app-shell.php#route=screens/dashboard.php'">
        <label>Usuario</label><input placeholder="admin">
        <div style="height:10px"></div>
        <label>Contraseña</label><input type="password" placeholder="••••••••">
        <div style="display:flex;justify-content:space-between;align-items:center;margin:12px 0 16px">
          <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" style="width:auto"> Recordarme</label>
          <a href="#">¿Olvidaste tu contraseña?</a>
        </div>
        <button class="btn">Entrar</button>
      </form>
      <p class="small" style="margin-top:14px">Demo sin base de datos.</p>
    </div>
    <div class="login-right">
      <div style='text-align:center'>
        <img src="images/logo-yoyjo.png" alt="Logo demo" style="width:300px;height:auto;opacity:.9">
        <div style='font-weight:800;font-size:22px;margin-bottom:8px'>Sistema de Cobranza</div>
      </div>
    </div>
  </div>
  <?php include __DIR__.'/includes/footer.php'; ?>
</body></html>
