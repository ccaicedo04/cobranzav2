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
        <span>COBRANZA</span>
    </div>
    <div class="sidebar-section">
        <p>Principal</p>
        <a href="index.php" class="sidebar-link<?= $routeActual === '' || $routeActual === 'dashboard' ? ' active' : '' ?>">
            <span>🏠</span> Dashboard
        </a>
    </div>
    <?php if (in_array('cobranzas', $modulos, true)): ?>
        <div class="sidebar-section">
            <p>Gestión</p>
            <a href="index.php?route=responsables" class="sidebar-link<?= $routeActual === 'responsables' ? ' active' : '' ?>"><span>👤</span> Responsables</a>
            <a href="index.php?route=estudiantes" class="sidebar-link<?= $routeActual === 'estudiantes' ? ' active' : '' ?>"><span>🎓</span> Estudiantes</a>
            <a href="index.php?route=deudas" class="sidebar-link<?= $routeActual === 'deudas' ? ' active' : '' ?>"><span>💼</span> Cartera</a>
            <a href="index.php?route=pagos" class="sidebar-link<?= $routeActual === 'pagos' ? ' active' : '' ?>"><span>💳</span> Pagos</a>
        </div>
        <div class="sidebar-section">
            <p>Comunicación</p>
            <a href="index.php?route=comunicaciones" class="sidebar-link<?= $routeActual === 'comunicaciones' ? ' active' : '' ?>"><span>💬</span> Mensajería</a>
        </div>
    <?php endif; ?>
    <?php if (in_array('administracion', $modulos, true) || in_array('parametrizacion', $modulos, true)): ?>
        <div class="sidebar-section">
            <p>Configuración</p>
            <?php if (in_array('administracion', $modulos, true)): ?>
                <a href="index.php?route=colegios" class="sidebar-link<?= $routeActual === 'colegios' ? ' active' : '' ?>"><span>🏫</span> Colegios</a>
                <a href="index.php?route=carga-masiva" class="sidebar-link<?= $routeActual === 'carga-masiva' ? ' active' : '' ?>"><span>📥</span> Carga Masiva</a>
                <a href="index.php?route=reportes" class="sidebar-link<?= $routeActual === 'reportes' ? ' active' : '' ?>"><span>📊</span> Reportes</a>
                <a href="index.php?route=auditoria" class="sidebar-link<?= $routeActual === 'auditoria' ? ' active' : '' ?>"><span>🛡️</span> Auditoría</a>
            <?php endif; ?>
            <?php if (in_array('parametrizacion', $modulos, true)): ?>
                <a href="index.php?route=configuracion" class="sidebar-link<?= $routeActual === 'configuracion' ? ' active' : '' ?>"><span>⚙️</span> Configuración</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="sidebar-footer">
        <a href="index.php?route=configuracion" class="sidebar-link"><span>⚙️</span> Configuración</a>
        <a href="index.php?route=auth/logout" class="sidebar-link danger" data-confirm="¿Deseas cerrar la sesión actual?"><span>🚪</span> Cerrar sesión</a>
    </div>
</aside>

<header class="topbar">
    <div class="topbar-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></div>
    <div class="topbar-search">
        <span aria-hidden="true">🔍</span>
        <input type="search" placeholder="Buscar responsables, estudiantes..." aria-label="Buscar">
    </div>
    <div class="topbar-context">
        <span>🏫 <?= htmlspecialchars($colegioNombre) ?></span>
        <small><?= htmlspecialchars($sedeNombre) ?></small>
    </div>
    <form class="topbar-tenant" method="post" action="<?= Helpers::baseUrl('index.php?route=contexto/actualizar') ?>" id="formContextoNav" data-base-path="<?= htmlspecialchars($basePath) ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($tokenNav) ?>">
        <input type="hidden" name="redirect" id="contextRedirect" value="<?= htmlspecialchars($redirectPath) ?>">
        <div>
            <label>Colegio</label>
            <select name="id_colegio" id="navColegio" title="Seleccionar colegio">
                <option value="">Todos mis colegios</option>
                <?php foreach ($colegiosDisponibles as $colegio): ?>
                    <option value="<?= $colegio['id_colegio'] ?>" <?= ($contexto['id_colegio'] ?? null) == $colegio['id_colegio'] ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
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
