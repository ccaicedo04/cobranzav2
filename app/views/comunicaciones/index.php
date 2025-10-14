<?php
$title = 'Comunicaciones';
$pageTitle = 'Comunicaciones';
$breadcrumbs = 'Cobranzas / Comunicaciones';
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
        <h3>Registro histórico</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Responsable</th>
                    <th>Canal</th>
                    <th>Asunto</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comunicaciones as $comunicacion): ?>
                    <tr>
                        <td><?= htmlspecialchars($comunicacion['fecha_envio']) ?></td>
                        <td><?= htmlspecialchars($mapResponsables[$comunicacion['id_responsable']] ?? $comunicacion['id_responsable']) ?></td>
                        <td><?= htmlspecialchars($comunicacion['canal']) ?></td>
                        <td><?= htmlspecialchars($comunicacion['asunto']) ?></td>
                        <td><?= htmlspecialchars($comunicacion['resultado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($comunicaciones)): ?>
                    <tr><td colspan="5">Sin comunicaciones registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Registrar comunicación</h3>
        <form method="post" action="index.php?route=comunicaciones/store" data-confirm="¿Confirmas el registro de esta gestión de comunicación?">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <label>Responsable</label>
            <select name="id_responsable" required>
                <?php foreach ($responsables as $responsable): ?>
                    <option value="<?= $responsable['id_responsable'] ?>"><?= htmlspecialchars($responsable['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Estudiante</label>
            <select name="id_estudiante">
                <option value="">Opcional</option>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <option value="<?= $estudiante['id_estudiante'] ?>"><?= htmlspecialchars($estudiante['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Canal</label>
            <select name="canal">
                <option value="whatsapp">WhatsApp</option>
                <option value="email">Email</option>
                <option value="sms">SMS</option>
                <option value="llamada">Llamada</option>
            </select>
            <label>Asunto</label>
            <input name="asunto" required>
            <label>Mensaje</label>
            <textarea name="mensaje" rows="4" required></textarea>
            <label>Resultado</label>
            <input name="resultado">
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button class="btn" type="submit">Registrar</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
