<?php
$title = 'Carga masiva';
$pageTitle = 'Carga masiva de datos';
$breadcrumbs = 'Cobranzas / Carga masiva';
include __DIR__ . '/../_partials/header.php';

$ventana = $ventana ?? [];
$ultima = $ventana['ultima'] ?? null;
$ultimaFecha = $ultima && !empty($ultima['fecha_registro']) ? date('Y-m-d H:i', strtotime((string) $ultima['fecha_registro'])) : 'Sin registros previos';
$ultimaArchivo = $ultima['archivo_original'] ?? 'N/D';
$ultimaResultado = $ultima['resultado'] ?? 'Pendiente';
$mapResultado = static function (string $estado): array {
    $estado = strtolower($estado);
    switch ($estado) {
        case 'exitoso':
        case 'ok':
            return ['label' => 'Exitoso', 'class' => 'success'];
        case 'parcial':
            return ['label' => 'Parcial', 'class' => 'neutral'];
        default:
            return ['label' => ucfirst($estado ?: 'Pendiente'), 'class' => 'danger'];
    }
};
?>

<div class="card" style="margin-bottom:20px;">
    <div>
        <h3 style="margin:0;">Ventana operativa de cargue masivo</h3>
        <p class="small" style="margin:6px 0 0;max-width:760px;">
            Cada inicio de mes se integra la base de cartera desde el módulo de cargue masivo. Durante la ventana vigente se ejecutan
            gestiones, comunicaciones y recaudos que alimentan los tableros de control y la planeación del siguiente ciclo.
        </p>
    </div>
    <div class="resume" style="margin-top:16px;">
        <div>
            <span>Última carga procesada</span>
            <strong><?= htmlspecialchars($ultimaFecha) ?></strong>
            <p class="small" style="margin:4px 0 0;">Archivo: <?= htmlspecialchars($ultimaArchivo) ?></p>
            <span class="tag <?= $mapResultado($ultimaResultado)['class'] ?>" style="margin-top:6px;display:inline-flex;">Estado: <?= htmlspecialchars($mapResultado($ultimaResultado)['label']) ?></span>
        </div>
        <div>
            <span>Registros acumulados</span>
            <strong><?= number_format((int) ($ventana['total_registros'] ?? 0), 0, ',', '.') ?></strong>
            <p class="small" style="margin:4px 0 0;">Suma de registros procesados en los cargues históricos.</p>
        </div>
        <div>
            <span>Errores detectados</span>
            <strong><?= number_format((int) ($ventana['total_errores'] ?? 0), 0, ',', '.') ?></strong>
            <p class="small" style="margin:4px 0 0;">Casos que requieren ajustes antes del siguiente ciclo.</p>
        </div>
        <div>
            <span>Gestiones registradas este mes</span>
            <strong><?= number_format((int) ($ventana['gestiones_mes'] ?? 0), 0, ',', '.') ?></strong>
            <p class="small" style="margin:4px 0 0;">Interacciones con responsables dentro de la ventana actual.</p>
        </div>
        <div>
            <span>Recaudo últimos 30 días</span>
            <strong>$ <?= number_format((float) ($ventana['recaudo_mes'] ?? 0), 0, ',', '.') ?></strong>
            <p class="small" style="margin:4px 0 0;">Valor consolidado de pagos aplicados.</p>
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);">
    <div class="card">
        <div class="history-header">
            <div>
                <h3 style="margin:0;">Línea de tiempo de cargues</h3>
                <p class="small" style="margin:4px 0 0;max-width:480px;">
                    Visualiza la evolución de los cargues mensuales y el estado de cada integración. Esta información sirve de respaldo para auditorías y comparativos de cartera.
                </p>
            </div>
        </div>
        <div class="table-scroll" style="max-height:420px;">
            <div class="timeline">
                <?php foreach ($cargas as $carga): ?>
                    <?php $estado = $mapResultado($carga['resultado'] ?? ''); ?>
                    <div class="timeline-item">
                        <h4><?= htmlspecialchars(date('Y-m-d H:i', strtotime((string) ($carga['fecha_registro'] ?? 'now')))) ?></h4>
                        <span><?= htmlspecialchars($carga['archivo_original'] ?? 'N/A') ?></span>
                        <div style="margin:6px 0;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                            <span class="tag <?= $estado['class'] ?>"><?= htmlspecialchars($estado['label']) ?></span>
                            <span class="small">Registros: <?= number_format((int) ($carga['total_registros'] ?? 0), 0, ',', '.') ?></span>
                            <span class="small">Errores: <?= number_format((int) ($carga['total_errores'] ?? 0), 0, ',', '.') ?></span>
                        </div>
                        <p class="small" style="margin:0;"><?= htmlspecialchars($carga['mensaje'] ?? 'Sin observaciones') ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($cargas)): ?>
                    <div class="timeline-item">
                        <h4>Sin registros</h4>
                        <p class="small" style="margin:0;">Aún no se han ejecutado cargues en el sistema.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card">
        <h3>Subir nueva base</h3>
        <p class="small" style="margin:4px 0 12px;">
            Selecciona el archivo en formato XLSX que corresponde al ciclo actual. El cargue validará la estructura y registrará un histórico para referencia futura.
        </p>
        <form method="post" action="index.php?route=carga-masiva/store" enctype="multipart/form-data" data-confirm="¿Deseas iniciar el proceso de carga masiva con el archivo seleccionado?" style="display:flex;flex-direction:column;gap:14px;">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <div>
                <label for="archivoCarga">Archivo Excel (.xlsx)</label>
                <input id="archivoCarga" type="file" name="archivo" accept=".xlsx" required>
            </div>
            <div>
                <label for="notasCarga">Notas internas (opcional)</label>
                <textarea id="notasCarga" name="notas" rows="3" placeholder="Ej: Base junio enviada por tesorería" style="resize:vertical;"></textarea>
            </div>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;">
                <a class="btn secondary" href="plantillas/plantilla_carga_masiva.php">Descargar plantilla</a>
                <button class="btn" type="submit">Registrar carga</button>
            </div>
        </form>
        <div style="margin-top:16px;padding:14px;border-radius:12px;background:#f1f5f9;">
            <strong style="display:block;font-size:13px;margin-bottom:6px;">Recomendaciones</strong>
            <ul class="small" style="margin:0;padding-left:18px;line-height:1.6;">
                <li>Incluye únicamente los responsables y estudiantes activos del ciclo.</li>
                <li>Verifica que las columnas de valores no contengan caracteres especiales.</li>
                <li>Si existe un ajuste o novedad, regístralo en las notas para facilitar el seguimiento.</li>
            </ul>
        </div>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <h3>Historial consolidado</h3>
    <div class="table-scroll" style="max-height:380px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Archivo</th>
                    <th>Resultado</th>
                    <th>Registros</th>
                    <th>Errores</th>
                    <th>Mensaje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cargas as $carga): ?>
                    <?php $estado = $mapResultado($carga['resultado'] ?? ''); ?>
                    <tr>
                        <td><?= htmlspecialchars($carga['fecha_registro'] ?? '') ?></td>
                        <td><?= htmlspecialchars($carga['archivo_original'] ?? '') ?></td>
                        <td><span class="tag <?= $estado['class'] ?>"><?= htmlspecialchars($estado['label']) ?></span></td>
                        <td><?= number_format((int) ($carga['total_registros'] ?? 0), 0, ',', '.') ?></td>
                        <td><?= number_format((int) ($carga['total_errores'] ?? 0), 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($carga['mensaje'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($cargas)): ?>
                    <tr><td colspan="6">Aún no se han realizado cargas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../_partials/footer.php'; ?>
