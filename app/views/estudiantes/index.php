<?php
$title = 'Estudiantes';
$pageTitle = 'Estudiantes';
$breadcrumbs = 'Cobranzas / Estudiantes';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form class="toolbar" method="get" action="index.php">
        <input type="hidden" name="route" value="estudiantes">
        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="activo" <?= (($filtros['estado'] ?? '') === 'activo') ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= (($filtros['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <span style="flex:1"></span>
        <a class="btn" href="index.php?route=estudiantes/create">Nuevo</a>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Grado</th>
                <th>Curso</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estudiantes as $estudiante): ?>
                <tr>
                    <td><?= htmlspecialchars($estudiante['codigo_estudiante']) ?></td>
                    <td><?= htmlspecialchars($estudiante['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($estudiante['grado']) ?></td>
                    <td><?= htmlspecialchars($estudiante['curso']) ?></td>
                    <td><?= htmlspecialchars($estudiante['estado']) ?></td>
                    <td>
                        <a class="btn" href="index.php?route=estudiantes/detalle&id=<?= $estudiante['id_estudiante'] ?>">Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($estudiantes)): ?>
                <tr><td colspan="6">No hay estudiantes registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
