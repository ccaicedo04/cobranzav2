<?php
use Core\Session;
$user = Session::get('user');
?>
<div class="appbar">
    <div class="container">
        <a href="index.php" style="display:flex;align-items:center;gap:8px;text-decoration:none;color:#fff;">
            <img src="images/logo-yoyjo.png" alt="Logo" style="height:40px;width:auto"/>
            <strong>Sistema de Cobranza</strong>
        </a>
        <div class="tenant">
            <span class="label">Colegio / Sede</span>
            <select disabled>
                <option><?= htmlspecialchars($user['id_colegio'] ?? 'Colegio Demo') ?></option>
            </select>
            <select disabled>
                <option><?= htmlspecialchars($user['id_sede'] ?? 'Sede Principal') ?></option>
            </select>
        </div>
        <nav class="nav">
            <div class="group">
                <button type="button">Cobranzas ▾</button>
                <div class="dropdown">
                    <a href="index.php?route=responsables" class="link">Responsables</a>
                    <a href="index.php?route=estudiantes" class="link">Estudiantes</a>
                    <a href="index.php?route=deudas" class="link">Deudas</a>
                    <a href="index.php?route=pagos" class="link">Pagos</a>
                    <a href="index.php?route=comunicaciones" class="link">Comunicaciones</a>
                    <a href="index.php?route=carga-masiva" class="link">Carga masiva</a>
                </div>
            </div>
            <div class="group">
                <button type="button">Administración ▾</button>
                <div class="dropdown">
                    <a href="index.php?route=colegios" class="link">Colegios</a>
                    <a href="index.php?route=sedes" class="link">Sedes</a>
                    <a href="index.php?route=usuarios" class="link">Usuarios</a>
                    <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:6px 0">
                    <a href="index.php?route=reportes" class="link">Reportes</a>
                    <a href="index.php?route=auditoria" class="link">Auditoría</a>
                </div>
            </div>
            <div class="group">
                <button type="button">Parametrización ▾</button>
                <div class="dropdown">
                    <a href="index.php?route=conceptos" class="link">Conceptos</a>
                    <a href="index.php?route=periodos" class="link">Períodos</a>
                    <a href="index.php?route=configuracion" class="link">Configuración</a>
                    <a href="index.php?route=plantillas" class="link">Plantillas</a>
                    <a href="index.php?route=parametros" class="link">Parámetros</a>
                </div>
            </div>
        </nav>
        <div class="userbox">
            <button type="button" id="userBtn"><?= htmlspecialchars($user['nombre_completo'] ?? 'Usuario') ?> ▾</button>
            <div class="menu" id="userMenu">
                <a href="index.php?route=perfil">Ver perfil</a>
                <a href="index.php?route=auth/logout">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>
<script>
const groups = document.querySelectorAll('.nav .group');
groups.forEach(group => {
    const dropdown = group.querySelector('.dropdown');
    if (!dropdown) return;
    group.addEventListener('mouseenter', () => dropdown.style.display = 'block');
    group.addEventListener('mouseleave', () => dropdown.style.display = 'none');
});
const userBtn = document.getElementById('userBtn');
const userMenu = document.getElementById('userMenu');
if (userBtn && userMenu) {
    userBtn.addEventListener('click', () => {
        userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', (ev) => {
        if (!userMenu.contains(ev.target) && ev.target !== userBtn) {
            userMenu.style.display = 'none';
        }
    });
}
</script>
