<?php
$title = 'Detalle colegio';
$pageTitle = $colegio['nombre'];
$breadcrumbs = 'Administración / Colegios / ' . $colegio['nombre'];
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card">
    <h3>Información institucional</h3>
    <p><strong>NIT:</strong> <?= htmlspecialchars($colegio['nit']) ?></p>
    <p><strong>Dirección:</strong> <?= htmlspecialchars($colegio['direccion'] ?: 'Sin registrar') ?></p>
    <p><strong>Teléfono:</strong> <?= htmlspecialchars($colegio['telefono'] ?: 'Sin registrar') ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($colegio['correo'] ?: 'Sin registrar') ?></p>
    <p><strong>Estado:</strong> <span class="badge" style="background:<?= $colegio['estado'] === 'activo' ? '#dcfce7' : '#fee2e2' ?>;color:<?= $colegio['estado'] === 'activo' ? '#166534' : '#991b1b' ?>;"><?= strtoupper($colegio['estado']) ?></span></p>
    <div class="actions" style="display:flex;gap:10px;margin-top:12px;">
        <a class="btn" href="index.php?route=colegios/edit&id=<?= $colegio['id_colegio'] ?>">Editar</a>
        <a class="btn secondary" href="index.php?route=colegios">Volver</a>
    </div>
</div>
<div class="card" style="margin-top:16px;">
    <h3>Sedes registradas</h3>
    <table class="table">
        <thead><tr><th>Nombre</th><th>Dirección</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
            <?php foreach ($sedes as $sede): ?>
                <tr>
                    <td><?= htmlspecialchars($sede['nombre']) ?></td>
                    <td><?= htmlspecialchars($sede['direccion'] ?: 'Sin dirección') ?></td>
                    <td><?= htmlspecialchars(strtoupper($sede['estado'])) ?></td>
                    <td><a class="btn" href="index.php?route=sedes/detalle&id=<?= $sede['id_sede'] ?>">Ver sede</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($sedes)): ?>
                <tr><td colspan="4">No hay sedes asociadas a este colegio.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
