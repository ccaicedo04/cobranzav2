<?php
$title = 'Detalle responsable';
$pageTitle = $responsable['nombre_completo'];
$breadcrumbs = 'Cobranzas / Responsables / ' . $responsable['nombre_completo'];
include __DIR__ . '/../_partials/header.php';
?>
<div class="grid grid-2" style="margin-bottom:20px;">
    <div class="card">
        <h3>Información general</h3>
        <p><strong>Documento:</strong> <?= htmlspecialchars($responsable['tipo_documento'] . ' ' . $responsable['numero_documento']) ?></p>
        <p><strong>Colegio:</strong> <?= htmlspecialchars($responsable['colegio_nombre'] ?? 'No asignado') ?></p>
        <p><strong>Sede:</strong> <?= htmlspecialchars($responsable['sede_nombre'] ?? 'No asignada') ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($responsable['telefono']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($responsable['correo']) ?></p>
        <p><strong>Dirección:</strong> <?= htmlspecialchars($responsable['direccion']) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($responsable['estado']) ?></p>
        <div style="margin-top:16px;display:flex;gap:10px;">
            <a class="btn" href="index.php?route=responsables/edit&id=<?= $responsable['id_responsable'] ?>">Editar</a>
            <form method="post" action="index.php?route=responsables/delete" onsubmit="return confirm('¿Deseas eliminar este responsable?');">
                <input type="hidden" name="id" value="<?= $responsable['id_responsable'] ?>">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(Core\Helpers::csrfToken()) ?>">
                <button class="btn secondary" type="submit">Eliminar</button>
            </form>
        </div>
    </div>
    <div class="card">
        <h3>Resumen de cartera</h3>
        <p><strong>Estudiantes vinculados:</strong> <?= count($estudiantes) ?></p>
        <p><strong>Total de deudas:</strong> $ <?= number_format(array_sum(array_column($deudas, 'saldo_actual')), 0, ',', '.') ?></p>
        <p><strong>Pagos registrados:</strong> <?= count($pagos) ?></p>
        <p><strong>Comunicaciones registradas:</strong> <?= count($comunicaciones) ?></p>
    </div>
</div>
<div class="card" style="margin-bottom:20px;">
    <h3>Estudiantes asociados</h3>
    <table class="table">
        <thead><tr><th>Nombre</th><th>Grado</th><th>Curso</th><th>Estado</th></tr></thead>
        <tbody>
            <?php foreach ($estudiantes as $estudiante): ?>
                <tr>
                    <td><?= htmlspecialchars($estudiante['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($estudiante['grado']) ?></td>
                    <td><?= htmlspecialchars($estudiante['curso']) ?></td>
                    <td><?= htmlspecialchars($estudiante['estado']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($estudiantes)): ?>
                <tr><td colspan="4">No hay estudiantes asociados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="card" style="margin-bottom:20px;">
    <h3>Deudas activas</h3>
    <table class="table">
        <thead><tr><th>Estudiante</th><th>Concepto</th><th>Saldo actual</th><th>Vencimiento</th><th>Estado</th></tr></thead>
        <tbody>
            <?php foreach ($deudas as $deuda): ?>
                <tr>
                    <td><?= htmlspecialchars($deuda['estudiante_nombre'] ?? $deuda['id_estudiante']) ?></td>
                    <td><?= htmlspecialchars($deuda['concepto_nombre'] ?? $deuda['id_concepto']) ?></td>
                    <td>$ <?= number_format($deuda['saldo_actual'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($deuda['fecha_vencimiento']) ?></td>
                    <td><?= htmlspecialchars($deuda['estado']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($deudas)): ?>
                <tr><td colspan="5">No hay deudas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="card">
    <h3>Pagos registrados</h3>
    <table class="table">
        <thead><tr><th>Fecha</th><th>Estudiante</th><th>Valor</th><th>Método</th><th>Referencia</th></tr></thead>
        <tbody>
            <?php foreach ($pagos as $pago): ?>
                <tr>
                    <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                    <td><?= htmlspecialchars($pago['estudiante_nombre'] ?? $pago['id_estudiante']) ?></td>
                    <td>$ <?= number_format($pago['valor_total'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                    <td><?= htmlspecialchars($pago['referencia']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($pagos)): ?>
                <tr><td colspan="5">No hay pagos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="card">
    <h3>Histórico de comunicaciones</h3>
    <table class="table">
        <thead><tr><th>Fecha</th><th>Canal</th><th>Asunto</th><th>Resultado</th></tr></thead>
        <tbody>
            <?php foreach ($comunicaciones as $comunicacion): ?>
                <tr>
                    <td><?= htmlspecialchars($comunicacion['fecha_envio']) ?></td>
                    <td><?= htmlspecialchars($comunicacion['canal']) ?></td>
                    <td><?= htmlspecialchars($comunicacion['asunto']) ?></td>
                    <td><?= htmlspecialchars($comunicacion['resultado']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($comunicaciones)): ?>
                <tr><td colspan="4">Sin comunicaciones registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
