<?php
$title = 'Registrar pago';
$pageTitle = $title;
$breadcrumbs = 'Cobranzas / Pagos / Registrar';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=pagos/store" enctype="multipart/form-data" data-confirm="¿Deseas registrar el pago con la información ingresada?">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <?php if (!empty($responsableId)): ?>
            <input type="hidden" name="responsable" value="<?= (int) $responsableId ?>">
        <?php endif; ?>
        <div class="two">
            <div>
                <label>Estudiante</label>
                <select name="id_estudiante" required>
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <option value="<?= $estudiante['id_estudiante'] ?>"><?= htmlspecialchars(($estudiante['responsable_nombre'] ?? '') . ' / ' . $estudiante['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($estudiantes)): ?>
                    <p class="text-muted">No hay estudiantes disponibles para el responsable seleccionado.</p>
                <?php endif; ?>
            </div>
            <div>
                <label>Fecha de pago</label>
                <input type="date" name="fecha_pago" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class="two">
            <div>
                <label>Valor total</label>
                <input type="number" name="valor_total" step="0.01" required>
            </div>
            <div>
                <label>Método de pago</label>
                <select name="metodo_pago">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Referencia</label>
                <input name="referencia">
            </div>
            <div>
                <label>Observaciones</label>
                <input name="observaciones">
            </div>
        </div>
        <div class="two">
            <div>
                <label>Soporte del pago (PDF o imagen)</label>
                <input type="file" name="soporte" accept="application/pdf,image/png,image/jpeg">
            </div>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <a class="btn secondary" href="index.php?route=pagos">Cancelar</a>
            <button class="btn" type="submit">Registrar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
