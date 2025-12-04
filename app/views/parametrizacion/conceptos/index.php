<?php
$title = 'Conceptos de deuda';
$pageTitle = 'Conceptos de cobro';
$breadcrumbs = 'Parametrización / Conceptos';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Catálogo de conceptos</h3>
        <table class="table">
            <thead><tr><th>Colegio</th><th>Nombre</th><th>Tipo</th><th>Valor base</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($conceptos as $concepto): ?>
                    <tr>
                        <td><?= htmlspecialchars($concepto['colegio_nombre']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($concepto['nombre']) ?></strong><br>
                            <small><?= htmlspecialchars($concepto['descripcion']) ?></small>
                        </td>
                        <td><?= htmlspecialchars(strtoupper($concepto['tipo'])) ?></td>
                        <td>$<?= number_format((float) $concepto['valor_base'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($concepto['estado']) ?></td>
                        <td><a class="btn" href="index.php?route=conceptos/edit&id=<?= $concepto['id_concepto'] ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($conceptos)): ?>
                    <tr><td colspan="6">No hay conceptos configurados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Crear concepto</h3>
        <form method="post" action="index.php?route=conceptos/store" data-confirm="¿Deseas registrar el nuevo concepto de cobro?">
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
            <label>Descripción</label>
            <textarea name="descripcion" rows="3"></textarea>
            <label>Tipo</label>
            <select name="tipo">
                <option value="recurrente">Recurrente</option>
                <option value="único">Único</option>
                <option value="servicio">Servicio</option>
            </select>
            <label>Valor base</label>
            <input type="number" name="valor_base" min="0" step="1000" required>
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
