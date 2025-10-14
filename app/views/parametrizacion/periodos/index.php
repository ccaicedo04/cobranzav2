<?php
$title = 'Períodos académicos';
$pageTitle = 'Períodos de facturación';
$breadcrumbs = 'Parametrización / Períodos';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Listado de períodos</h3>
        <table class="table">
            <thead><tr><th>Colegio</th><th>Nombre</th><th>Fechas</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($periodos as $periodo): ?>
                    <tr>
                        <td><?= htmlspecialchars($periodo['colegio_nombre']) ?></td>
                        <td><?= htmlspecialchars($periodo['nombre']) ?></td>
                        <td><?= htmlspecialchars($periodo['fecha_inicio']) ?> - <?= htmlspecialchars($periodo['fecha_fin']) ?></td>
                        <td><?= htmlspecialchars($periodo['estado']) ?></td>
                        <td><a class="btn" href="index.php?route=periodos/edit&id=<?= $periodo['id_periodo'] ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($periodos)): ?>
                    <tr><td colspan="5">No hay períodos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Crear período</h3>
        <form method="post" action="index.php?route=periodos/store">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <?php if (($usuario['rol'] ?? '') === 'admin_global'): ?>
                <label>Colegio</label>
                <select name="id_colegio" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($colegios as $colegio): ?>
                        <option value="<?= $colegio['id_colegio'] ?>"><?= htmlspecialchars($colegio['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <label>Nombre</label>
            <input name="nombre" required>
            <label>Fecha inicio</label>
            <input type="date" name="fecha_inicio" required>
            <label>Fecha fin</label>
            <input type="date" name="fecha_fin" required>
            <label>Estado</label>
            <select name="estado">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
