<?php
use Core\Session;

$user = Session::get('user');
$contexto = Session::get('context', []);
$tokenNav = Session::get('contexto_token');
if (!$tokenNav) {
    $tokenNav = bin2hex(random_bytes(16));
    Session::set('contexto_token', $tokenNav);
}
$colegiosDisponibles = $user['colegios_disponibles'] ?? [];
$sedesDisponibles = $user['sedes_disponibles'] ?? [];
$modulos = $user['modulos_permitidos'] ?? [];
?>
<div class="appbar">
    <div class="container">
        <a href="index.php" style="display:flex;align-items:center;gap:8px;text-decoration:none;color:#fff;">
            <img src="images/logo-yoyjo.png" alt="Logo" style="height:40px;width:auto"/>
            <strong>Sistema de Cobranza</strong>
        </a>
        <form class="tenant" method="post" action="index.php?route=contexto/actualizar">
            <span class="label">Contexto</span>
            <input type="hidden" name="_token" value="<?= htmlspecialchars($tokenNav) ?>">
            <select name="id_colegio" onchange="this.form.submit()">
                <option value="">Todos mis colegios</option>
                <?php foreach ($colegiosDisponibles as $colegio): ?>
                    <option value="<?= $colegio['id_colegio'] ?>" <?= ($contexto['id_colegio'] ?? null) == $colegio['id_colegio'] ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_sede" onchange="this.form.submit()">
                <option value="">Todas mis sedes</option>
                <?php foreach ($sedesDisponibles as $sede): ?>
                    <?php
                        $oculto = ($contexto['id_colegio'] ?? null) && $contexto['id_colegio'] !== $sede['id_colegio'];
                    ?>
                    <option value="<?= $sede['id_sede'] ?>" <?= $oculto ? 'hidden' : '' ?> <?= ($contexto['id_sede'] ?? null) == $sede['id_sede'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars(($sede['nombre'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <nav class="nav">
            <?php if (in_array('cobranzas', $modulos, true)): ?>
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
            <?php endif; ?>
            <?php if (in_array('administracion', $modulos, true)): ?>
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
            <?php endif; ?>
            <?php if (in_array('parametrizacion', $modulos, true)): ?>
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
            <?php endif; ?>
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
