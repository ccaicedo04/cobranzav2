<?php
$title = isset($estudiante) ? 'Editar estudiante' : 'Nuevo estudiante';
$pageTitle = $title;
$breadcrumbs = 'Cobranzas / Estudiantes / ' . $title;
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <form method="post" action="index.php?route=estudiantes/<?= isset($estudiante) ? 'update' : 'store' ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
        <?php if (isset($estudiante)): ?>
            <input type="hidden" name="id_estudiante" value="<?= $estudiante['id_estudiante'] ?>">
        <?php endif; ?>
        <input type="hidden" name="id_colegio" id="campoColegio" value="<?= htmlspecialchars($estudiante['id_colegio'] ?? '') ?>">
        <div class="two">
            <div>
                <label>Nombre completo</label>
                <input name="nombre_completo" value="<?= htmlspecialchars($estudiante['nombre_completo'] ?? '') ?>" required>
            </div>
            <div>
                <label>Código</label>
                <input name="codigo_estudiante" value="<?= htmlspecialchars($estudiante['codigo_estudiante'] ?? '') ?>" required>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Grado</label>
                <input name="grado" value="<?= htmlspecialchars($estudiante['grado'] ?? '') ?>" required>
            </div>
            <div>
                <label>Curso</label>
                <input name="curso" value="<?= htmlspecialchars($estudiante['curso'] ?? '') ?>" required>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Responsable financiero</label>
                <select name="id_responsable" id="responsableSelector" onchange="sincronizarResponsable()" required>
                    <?php foreach ($responsables as $responsable): ?>
                        <option value="<?= $responsable['id_responsable'] ?>" data-colegio="<?= $responsable['id_colegio'] ?>" data-sede="<?= $responsable['id_sede'] ?>" <?= (($estudiante['id_responsable'] ?? '') == $responsable['id_responsable']) ? 'selected' : '' ?>><?= htmlspecialchars($responsable['nombre_completo'] . ' (' . ($responsable['colegio_nombre'] ?? 'Sin colegio') . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Estado</label>
                <?php $estado = $estudiante['estado'] ?? 'activo'; ?>
                <select name="estado">
                    <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>
        <div class="two">
            <div>
                <label>Sede</label>
                <select name="id_sede" id="sedeSelector">
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id_sede'] ?>" data-colegio="<?= $sede['id_colegio'] ?>" <?= (($estudiante['id_sede'] ?? '') == $sede['id_sede']) ? 'selected' : '' ?>><?= htmlspecialchars(($sede['colegio_nombre'] ?? '') . ' - ' . $sede['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div></div>
        </div>
        <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
            <a class="btn secondary" href="index.php?route=estudiantes">Cancelar</a>
            <button class="btn" type="submit">Guardar</button>
        </div>
    </form>
</div>
<script>
function sincronizarResponsable() {
    const responsable = document.getElementById('responsableSelector');
    const sede = document.getElementById('sedeSelector');
    const colegioCampo = document.getElementById('campoColegio');
    if (!responsable || !sede || !colegioCampo) return;
    const opcion = responsable.options[responsable.selectedIndex];
    const colegio = opcion.dataset.colegio || '';
    const sedeResp = opcion.dataset.sede || '';
    colegioCampo.value = colegio;
    [...sede.options].forEach(op => {
        if (!op.value) return;
        op.hidden = colegio && op.dataset.colegio !== colegio;
    });
    if (colegio) {
        const match = [...sede.options].find(op => !op.hidden && (op.value === sedeResp || sedeResp === ''));
        if (match) {
            sede.value = match.value;
        }
    }
}

sincronizarResponsable();
</script>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
