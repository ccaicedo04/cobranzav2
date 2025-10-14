<?php
$title = 'Responsables';
$pageTitle = 'Responsables';
$breadcrumbs = 'Cobranzas / Responsables';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form class="toolbar" method="get" action="index.php">
        <input type="hidden" name="route" value="responsables">
        <input name="documento" style="max-width:260px" placeholder="Buscar por documento" value="<?= htmlspecialchars($filtros['numero_documento'] ?? '') ?>">
        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="activo" <?= (($filtros['estado'] ?? '') === 'activo') ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= (($filtros['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <span style="flex:1"></span>
        <a class="btn" href="index.php?route=responsables/create">Nuevo</a>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Colegio</th>
                <th>Sede</th>
                <th>Contacto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responsables as $responsable): ?>
                <tr>
                    <td><?= htmlspecialchars($responsable['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($responsable['numero_documento']) ?></td>
                    <td><?= htmlspecialchars($responsable['colegio_nombre']) ?></td>
                    <td><?= htmlspecialchars($responsable['sede_nombre']) ?></td>
                    <td>
                        <div><?= htmlspecialchars($responsable['telefono'] ?: 'Sin teléfono') ?></div>
                        <small><?= htmlspecialchars($responsable['correo'] ?: 'Sin correo') ?></small>
                    </td>
                    <td><span class="badge" style="background:<?= $responsable['estado'] === 'activo' ? '#dcfce7' : '#fee2e2' ?>;color:<?= $responsable['estado'] === 'activo' ? '#166534' : '#991b1b' ?>;">
                        <?= strtoupper($responsable['estado']) ?></span></td>
                    <td>
                        <a class="btn" href="index.php?route=responsables/detalle&id=<?= $responsable['id_responsable'] ?>">Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($responsables)): ?>
                <tr><td colspan="7">No se encontraron responsables con los filtros seleccionados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
