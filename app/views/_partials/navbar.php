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
$sedesAgrupadas = [];
foreach ($sedesDisponibles as $sedeDisponible) {
    $sedesAgrupadas[$sedeDisponible['id_colegio']][] = $sedeDisponible;
}
$modulos = $user['modulos_permitidos'] ?? [];
$colegioActual = 'Todos mis colegios';
$sedeActual = 'Todas mis sedes';
if (!empty($contexto['id_colegio'])) {
    foreach ($colegiosDisponibles as $colegio) {
        if ((int) $colegio['id_colegio'] === (int) $contexto['id_colegio']) {
            $colegioActual = $colegio['nombre'];
            break;
        }
    }
} elseif (count($colegiosDisponibles) === 1) {
    $colegioActual = $colegiosDisponibles[0]['nombre'];
}

if (!empty($contexto['id_sede'])) {
    foreach ($sedesDisponibles as $sede) {
        if ((int) $sede['id_sede'] === (int) $contexto['id_sede']) {
            $sedeActual = $sede['nombre'];
            break;
        }
    }
} elseif (count($sedesDisponibles) === 1) {
    $sedeActual = $sedesDisponibles[0]['nombre'];
}
?>
<div class="appbar">
    <div class="container">
        <a href="index.php" style="display:flex;align-items:center;gap:8px;text-decoration:none;color:#fff;">
            <img src="images/logo-yoyjo.png" alt="Logo" style="height:40px;width:auto"/>
            <strong>Sistema de Cobranza</strong>
        </a>
        <form class="tenant" method="post" action="index.php?route=contexto/actualizar" id="formContextoNav">
            <span class="label">Contexto</span>
            <input type="hidden" name="_token" value="<?= htmlspecialchars($tokenNav) ?>">
            <div class="tenant-summary">
                <span class="tenant-pill" title="Colegio en uso"><?= htmlspecialchars($colegioActual) ?></span>
                <span class="tenant-pill" title="Sede en uso"><?= htmlspecialchars($sedeActual) ?></span>
            </div>
            <select name="id_colegio" id="navColegio">
                <option value="">Todos mis colegios</option>
                <?php foreach ($colegiosDisponibles as $colegio): ?>
                    <option value="<?= $colegio['id_colegio'] ?>" <?= ($contexto['id_colegio'] ?? null) == $colegio['id_colegio'] ? 'selected' : '' ?>><?= htmlspecialchars($colegio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_sede" id="navSede">
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
