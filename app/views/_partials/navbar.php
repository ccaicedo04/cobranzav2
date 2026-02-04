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
?>
<div class="appbar">
    <div class="container">
        <a href="<?= Helpers::baseUrl('index.php') ?>" class="brand-link">
            <img src="images/logo-yoyjo.png" alt="Logo" />
            <strong>Sistema de Cobranza</strong>
        </a>
        <form class="tenant" method="post" action="<?= Helpers::baseUrl('index.php?route=contexto/actualizar') ?>" id="formContextoNav" data-base-path="<?= htmlspecialchars($basePath) ?>">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($tokenNav) ?>">
            <input type="hidden" name="redirect" id="contextRedirect" value="<?= htmlspecialchars($redirectPath) ?>">
            <div class="tenant-field">
                <span class="tenant-field-label">Colegio</span>
                <select name="id_colegio" id="navColegio" title="Seleccionar colegio">
                    <option value="">Todos mis colegios</option>
                    <?php foreach ($colegiosDisponibles as $colegio): ?>
                        <option value="<?= $colegio['id_colegio'] ?>" <?= ($contexto['id_colegio'] ?? null) == $colegio['id_colegio'] ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tenant-field">
                <span class="tenant-field-label">Sede</span>
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
        <nav class="nav">
            <?php if (in_array('cobranzas', $modulos, true)): ?>
                <div class="group">
                    <button type="button" class="nav-toggle" data-dropdown-toggle aria-expanded="false">Cobranzas <span aria-hidden="true">▾</span></button>
                    <div class="dropdown" role="menu">
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
                    <button type="button" class="nav-toggle" data-dropdown-toggle aria-expanded="false">Administración <span aria-hidden="true">▾</span></button>
                    <div class="dropdown" role="menu">
                        <a href="index.php?route=colegios" class="link">Colegios</a>
                        <a href="index.php?route=sedes" class="link">Sedes</a>
                        <a href="index.php?route=usuarios" class="link">Usuarios</a>
                        <?php if (($user['rol'] ?? '') === 'admin_global'): ?>
                            <a href="index.php?route=mantenimiento/limpiar-cobranzas" class="link">Limpieza cobranzas</a>
                        <?php endif; ?>
                        <hr style="border:none;border-top:1px solid rgba(255,255,255,.12);margin:6px 0">
                        <a href="index.php?route=reportes" class="link">Reportes</a>
                        <a href="index.php?route=auditoria" class="link">Auditoría</a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (in_array('parametrizacion', $modulos, true)): ?>
                <div class="group">
                    <button type="button" class="nav-toggle" data-dropdown-toggle aria-expanded="false">Parametrización <span aria-hidden="true">▾</span></button>
                    <div class="dropdown" role="menu">
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
            <button type="button" id="userBtn" class="user-toggle" aria-haspopup="true" aria-expanded="false"><?= htmlspecialchars($user['nombre_completo'] ?? 'Usuario') ?> ▾</button>
            <div class="menu" id="userMenu" role="menu">
                <a href="index.php?route=perfil">Ver perfil</a>
                <a href="index.php?route=auth/logout" data-confirm="¿Deseas cerrar la sesión actual?">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>
