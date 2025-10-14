<?php
$title = 'Registrar deuda';
$pageTitle = $title;
$breadcrumbs = 'Cobranzas / Deudas / Registrar';
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=deudas/store">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <div class="two">
            <div>
                <label>Estudiante</label>
                <select name="id_estudiante" required>
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <option value="<?= $estudiante['id_estudiante'] ?>"><?= htmlspecialchars($estudiante['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Concepto</label>
                <select name="id_concepto" required>
                    <?php foreach ($conceptos as $concepto): ?>
                        <option value="<?= $concepto['id_concepto'] ?>"><?= htmlspecialchars($concepto['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Período</label>
                <select name="id_periodo">
                    <option value="">Seleccione</option>
                    <?php foreach ($periodos as $periodo): ?>
                        <option value="<?= $periodo['id_periodo'] ?>"><?= htmlspecialchars($periodo['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Fecha de generación</label>
                <input type="date" name="fecha_generacion" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class="two">
            <div>
                <label>Valor inicial</label>
                <input type="number" name="valor_inicial" step="0.01" required>
            </div>
            <div>
                <label>Saldo actual</label>
                <input type="number" name="saldo_actual" step="0.01">
            </div>
        </div>
        <div class="two">
            <div>
                <label>Fecha de vencimiento</label>
                <input type="date" name="fecha_vencimiento">
            </div>
            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagado">Pagado</option>
                    <option value="en_acuerdo">En acuerdo</option>
                </select>
            </div>
        </div>
        <div>
            <label>Notas</label>
            <textarea name="notas" rows="3"></textarea>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <a class="btn secondary" href="index.php?route=deudas">Cancelar</a>
            <button class="btn" type="submit">Guardar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
