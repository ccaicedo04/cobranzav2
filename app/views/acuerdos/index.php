<?php
$title = 'Acuerdos de pago';
$pageTitle = 'Acuerdos de pago';
$breadcrumbs = 'Cobranzas / Acuerdos';
include __DIR__ . '/../_partials/header.php';
?>
<?php
$mapResponsables = [];
foreach ($responsables as $responsable) {
    $mapResponsables[$responsable['id_responsable']] = $responsable['nombre_completo'];
}
$mapEstudiantes = [];
foreach ($estudiantes as $estudiante) {
    $mapEstudiantes[$estudiante['id_estudiante']] = $estudiante['nombre_completo'];
}
?>
<div class="grid" style="grid-template-columns:2fr 1fr;">
    <div class="card">
        <h3>Acuerdos vigentes</h3>
        <?php if (!empty($responsableId)): ?>
            <?php
                $responsableSeleccionado = array_filter($responsables, fn($item) => $item['id_responsable'] == $responsableId);
                $responsableSeleccionado = array_shift($responsableSeleccionado);
            ?>
            <p class="alert">Filtrando estudiantes por responsable <?= htmlspecialchars($responsableSeleccionado['nombre_completo'] ?? ('#' . $responsableId)) ?>.</p>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Responsable</th>
                    <th>Estudiante</th>
                    <th>Monto total</th>
                    <th>Cuotas</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($acuerdos as $acuerdo): ?>
                    <tr>
                        <td><?= htmlspecialchars($mapResponsables[$acuerdo['id_responsable']] ?? $acuerdo['id_responsable']) ?></td>
                        <td><?= htmlspecialchars($mapEstudiantes[$acuerdo['id_estudiante']] ?? '-') ?></td>
                        <td>$ <?= number_format($acuerdo['monto_total'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($acuerdo['cuotas']) ?></td>
                        <td><?= htmlspecialchars($acuerdo['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($acuerdos)): ?>
                    <tr><td colspan="5">No hay acuerdos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Crear acuerdo</h3>
        <form method="post" action="index.php?route=acuerdos/store">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <?php if (!empty($responsableId)): ?>
                <input type="hidden" name="responsable" value="<?= (int) $responsableId ?>">
            <?php endif; ?>
            <label>Responsable</label>
            <select name="id_responsable" required>
                <?php foreach ($responsables as $responsable): ?>
                    <option value="<?= $responsable['id_responsable'] ?>" <?= (!empty($responsableId) && $responsableId == $responsable['id_responsable']) ? 'selected' : '' ?>><?= htmlspecialchars($responsable['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Estudiante</label>
            <select name="id_estudiante">
                <option value="">Opcional</option>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <option value="<?= $estudiante['id_estudiante'] ?>"><?= htmlspecialchars($estudiante['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Monto total</label>
            <input type="number" name="monto_total" step="0.01" required>
            <label>Número de cuotas</label>
            <input type="number" name="cuotas" min="1" value="1" required>
            <label>Fecha inicio</label>
            <input type="date" name="fecha_inicio" value="<?= date('Y-m-d') ?>">
            <label>Fecha fin</label>
            <input type="date" name="fecha_fin">
            <label>Estado</label>
            <select name="estado">
                <option value="activo">Activo</option>
                <option value="cerrado">Cerrado</option>
            </select>
            <label>Observaciones</label>
            <textarea name="observaciones" rows="3"></textarea>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
