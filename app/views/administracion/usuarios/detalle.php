<?php
$title = 'Detalle usuario';
$pageTitle = 'Detalle de usuario';
$breadcrumbs = 'Administración / Usuarios / Detalle';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:1.2fr 1fr;gap:18px;">
    <div class="card">
        <h3>Información general</h3>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuarioDetalle['nombre_completo']) ?></p>
        <p><strong>Usuario:</strong> <?= htmlspecialchars($usuarioDetalle['usuario']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($usuarioDetalle['email']) ?></p>
        <p><strong>Rol:</strong> <?= htmlspecialchars(strtoupper($usuarioDetalle['rol'])) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($usuarioDetalle['estado']) ?></p>
        <p><strong>Último colegio base:</strong> <?= htmlspecialchars($usuarioDetalle['colegio_nombre'] ?? 'Sin definir') ?></p>
        <p><strong>Última sede base:</strong> <?= htmlspecialchars($usuarioDetalle['sede_nombre'] ?? 'Sin definir') ?></p>
        <div class="actions" style="margin-top:12px;display:flex;gap:10px;">
            <a class="btn" href="index.php?route=usuarios">Volver</a>
        </div>
    </div>
    <div class="card">
        <h3>Módulos habilitados</h3>
        <?php if (!empty($modulos)): ?>
            <ul>
                <?php foreach ($modulos as $modulo): ?>
                    <li><?= htmlspecialchars(ucfirst($modulo)) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay módulos asociados.</p>
        <?php endif; ?>
    </div>
</div>
<div class="grid" style="grid-template-columns:1fr 1fr;gap:18px;margin-top:18px;">
    <div class="card">
        <h3>Colegios asignados</h3>
        <?php if (!empty($colegios)): ?>
            <ul>
                <?php foreach ($colegios as $colegio): ?>
                    <li><?= htmlspecialchars($colegio['nombre']) ?> (NIT: <?= htmlspecialchars($colegio['nit'] ?? 'N/A') ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Sin colegios asignados.</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Sedes asignadas</h3>
        <?php if (!empty($sedes)): ?>
            <ul>
                <?php foreach ($sedes as $sede): ?>
                    <li><?= htmlspecialchars(($sede['colegio_nombre'] ?? '') . ' - ' . $sede['nombre']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Sin sedes asignadas.</p>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
