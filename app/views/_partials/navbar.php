<?php
use Core\Helpers;
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
$sedesAgrupadas = [];
foreach ($sedesDisponibles as $sedeDisponible) {
    $sedesAgrupadas[$sedeDisponible['id_colegio']][] = $sedeDisponible;
}
$modulos = $user['modulos_permitidos'] ?? [];
$query = $_GET;
$routeActual = $query['route'] ?? '';
if ($routeActual !== '') {
    unset($query['route']);
}
$redirectParams = [];
if ($routeActual !== '') {
    $redirectParams['route'] = $routeActual;
}
if (!empty($query)) {
    foreach ($query as $clave => $valor) {
        $redirectParams[$clave] = $valor;
    }
}
$redirectPath = 'index.php';
if (!empty($redirectParams)) {
    $redirectPath .= '?' . http_build_query($redirectParams);
}
$basePath = rtrim(Helpers::baseUrl(), '/');
$usuarioNombre = $user['nombre_completo'] ?? 'Usuario';
$usuarioRol = ucfirst(str_replace('_', ' ', (string) ($user['rol'] ?? 'usuario')));
$colegioNombre = $contexto['colegio_nombre'] ?? ($user['colegio_nombre'] ?? 'Colegio');
$sedeNombre = $contexto['sede_nombre'] ?? ($user['sede_nombre'] ?? 'Sede');
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🏫</div>
        <span class="brand-text">COBRANZA</span>
        <button class="sidebar-toggle" type="button" aria-label="Contraer menú">⇔</button>
    </div>
    <div class="sidebar-section open" data-section>
        <button class="sidebar-section-toggle" type="button">
            <span>Principal</span>
            <span class="chevron">▾</span>
        </button>
        <a href="index.php" class="sidebar-link<?= $routeActual === '' || $routeActual === 'dashboard' ? ' active' : '' ?>" title="Dashboard">
            <span class="icon">🏠</span><span class="label">Dashboard</span>
        </a>
    </div>
    <?php if (in_array('cobranzas', $modulos, true)): ?>
        <div class="sidebar-section open" data-section>
            <button class="sidebar-section-toggle" type="button">
                <span>Gestión</span>
                <span class="chevron">▾</span>
            </button>
            <a href="index.php?route=responsables" class="sidebar-link<?= $routeActual === 'responsables' ? ' active' : '' ?>" title="Responsables"><span class="icon">👤</span><span class="label">Responsables</span></a>
            <a href="index.php?route=estudiantes" class="sidebar-link<?= $routeActual === 'estudiantes' ? ' active' : '' ?>" title="Estudiantes"><span class="icon">🎓</span><span class="label">Estudiantes</span></a>
            <a href="index.php?route=deudas" class="sidebar-link<?= $routeActual === 'deudas' ? ' active' : '' ?>" title="Cartera"><span class="icon">💼</span><span class="label">Cartera</span></a>
            <a href="index.php?route=pagos" class="sidebar-link<?= $routeActual === 'pagos' ? ' active' : '' ?>" title="Pagos"><span class="icon">💳</span><span class="label">Pagos</span></a>
        </div>
        <div class="sidebar-section open" data-section>
            <button class="sidebar-section-toggle" type="button">
                <span>Comunicación</span>
                <span class="chevron">▾</span>
            </button>
            <a href="index.php?route=comunicaciones" class="sidebar-link<?= $routeActual === 'comunicaciones' ? ' active' : '' ?>" title="Mensajería"><span class="icon">💬</span><span class="label">Mensajería</span></a>
        </div>
    <?php endif; ?>
    <?php if (in_array('administracion', $modulos, true) || in_array('parametrizacion', $modulos, true)): ?>
        <div class="sidebar-section open" data-section>
            <button class="sidebar-section-toggle" type="button">
                <span>Configuración</span>
                <span class="chevron">▾</span>
            </button>
            <?php if (in_array('administracion', $modulos, true)): ?>
                <a href="index.php?route=colegios" class="sidebar-link<?= $routeActual === 'colegios' ? ' active' : '' ?>" title="Colegios"><span class="icon">🏫</span><span class="label">Colegios</span></a>
                <a href="index.php?route=carga-masiva" class="sidebar-link<?= $routeActual === 'carga-masiva' ? ' active' : '' ?>" title="Carga Masiva"><span class="icon">📥</span><span class="label">Carga Masiva</span></a>
                <a href="index.php?route=reportes" class="sidebar-link<?= $routeActual === 'reportes' ? ' active' : '' ?>" title="Reportes"><span class="icon">📊</span><span class="label">Reportes</span></a>
                <a href="index.php?route=auditoria" class="sidebar-link<?= $routeActual === 'auditoria' ? ' active' : '' ?>" title="Auditoría"><span class="icon">🛡️</span><span class="label">Auditoría</span></a>
            <?php endif; ?>
            <?php if (in_array('parametrizacion', $modulos, true)): ?>
                <a href="index.php?route=configuracion" class="sidebar-link<?= $routeActual === 'configuracion' ? ' active' : '' ?>" title="Configuración"><span class="icon">⚙️</span><span class="label">Configuración</span></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="sidebar-footer">
        <a href="index.php?route=configuracion" class="sidebar-link" title="Configuración"><span class="icon">⚙️</span><span class="label">Configuración</span></a>
        <a href="index.php?route=auth/logout" class="sidebar-link danger" title="Cerrar sesión" data-confirm="¿Deseas cerrar la sesión actual?"><span class="icon">🚪</span><span class="label">Cerrar sesión</span></a>
    </div>
</aside>

<header class="topbar">
    <div class="topbar-left">
        <div class="topbar-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></div>
        <div class="topbar-context">
            <span>🏫 <?= htmlspecialchars($colegioNombre) ?></span>
            <form class="topbar-tenant" method="post" action="<?= Helpers::baseUrl('index.php?route=contexto/actualizar') ?>" id="formContextoNav" data-base-path="<?= htmlspecialchars($basePath) ?>">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($tokenNav) ?>">
                <input type="hidden" name="redirect" id="contextRedirect" value="<?= htmlspecialchars($redirectPath) ?>">
                <div>
                    <label>Sede</label>
                    <select name="id_sede" id="navSede" title="Seleccionar sede">
                        <option value="">Todas mis sedes</option>
                        <?php foreach ($colegiosDisponibles as $colegio): ?>
                            <?php $sedesColegio = $sedesAgrupadas[$colegio['id_colegio']] ?? []; ?>
                            <?php if ($sedesColegio): ?>
                                <optgroup label="<?= htmlspecialchars($colegio['nombre']) ?>" data-colegio="<?= $colegio['id_colegio'] ?>">
                                    <?php foreach ($sedesColegio as $sede): ?>
                                        <option value="<?= $sede['id_sede'] ?>" <?= ($contexto['id_sede'] ?? null) == $sede['id_sede'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($sede['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="topbar-actions">
        <button class="topbar-icon" type="button" aria-label="Notificaciones">
            🔔
            <span class="dot"></span>
        </button>
        <div class="topbar-user">
            <div>
                <strong><?= htmlspecialchars($usuarioNombre) ?></strong>
                <span><?= htmlspecialchars($usuarioRol) ?></span>
            </div>
            <div class="avatar">👤</div>
            <div class="topbar-user-menu">
                <a href="index.php?route=perfil">Ver perfil</a>
                <a href="index.php?route=auth/logout" data-confirm="¿Deseas cerrar la sesión actual?">Cerrar sesión</a>
            </div>
        </div>
    </div>
</header>
