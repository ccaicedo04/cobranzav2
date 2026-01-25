<?php
$title = 'Carga masiva';
$pageTitle = 'Carga masiva de datos';
$breadcrumbs = 'Cobranzas / Carga masiva';
include __DIR__ . '/../_partials/header.php';

$ventana = $ventana ?? [];
$colegios = $colegios ?? [];
$contexto = \Core\Session::get('context') ?? [];
$colegioSeleccionado = $contexto['id_colegio'] ?? ($colegios[0]['id_colegio'] ?? '');
$anioSugerido = date('Y');
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
                            <span class="small">Valor cargado: $ <?= number_format((float) ($carga['total_registros'] ?? 0), 0, ',', '.') ?></span>
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
            Selecciona el archivo en formato XLSX o XLSM que corresponde al ciclo actual. El cargue validará la estructura y mostrará una previsualización antes de guardar.
        </p>
        <form method="post" action="index.php?route=carga-masiva/store" enctype="multipart/form-data" data-confirm="¿Deseas iniciar el proceso de carga masiva con el archivo seleccionado?" style="display:flex;flex-direction:column;gap:14px;">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                <div>
                    <label for="colegioCarga">Colegio</label>
                    <select id="colegioCarga" name="id_colegio" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($colegios as $colegio): ?>
                            <option value="<?= htmlspecialchars($colegio['id_colegio']) ?>" <?= (string) $colegioSeleccionado === (string) ($colegio['id_colegio'] ?? '') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($colegio['nombre'] ?? 'Colegio') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="anioCarga">Año</label>
                    <input id="anioCarga" type="number" name="anio" min="2000" max="2100" value="<?= htmlspecialchars($anioSugerido) ?>" required>
                </div>
            </div>
            <div>
                <label for="archivoCarga">Archivo Excel (.xlsx, .xlsm)</label>
                <input id="archivoCarga" type="file" name="archivo" accept=".xlsx,.xlsm" required>
                <p class="small" style="margin:4px 0 0;">Extensiones permitidas .xlsx, .xlsm | Tamaño máximo 10 MB.</p>
            </div>
            <div>
                <label for="notasCarga">Notas internas (opcional)</label>
                <textarea id="notasCarga" name="notas" rows="3" placeholder="Ej: Base junio enviada por tesorería" style="resize:vertical;"></textarea>
            </div>
            <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;">
                <a class="btn secondary" href="plantillas/plantilla_carga_masiva.php">Descargar plantilla</a>
                <button class="btn" type="submit" name="modo" value="preview">Previsualizar carga</button>
            </div>
        </form>
        <div style="margin-top:16px;padding:14px;border-radius:12px;background:#f1f5f9;">
            <strong style="display:block;font-size:13px;margin-bottom:6px;">Recomendaciones</strong>
            <ul class="small" style="margin:0;padding-left:18px;line-height:1.6;">
                <li>Incluye únicamente los responsables y estudiantes activos del ciclo.</li>
                <li>La sede se asigna automáticamente según el código del estudiante (5 dígitos Bogotá, 4 dígitos Cota).</li>
                <li>Verifica que las columnas de valores no contengan caracteres especiales.</li>
                <li>Si existe un ajuste o novedad, regístralo en las notas para facilitar el seguimiento.</li>
            </ul>
        </div>
    </div>
</div>

<?php if (!empty($preview_error)): ?>
    <div class="alert error" style="margin-top:20px;">
        <strong>Error en la previsualización:</strong>
        <span><?= htmlspecialchars($preview_error) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($preview)): ?>
    <?php
    $resumen = $preview['resultado'] ?? [];
    $errores = $resumen['errores'] ?? [];
    $totalesMeses = $resumen['totales_meses'] ?? [];
    $totalesSedes = $resumen['totales_sedes'] ?? [];
    ksort($totalesMeses);
    arsort($totalesSedes);
    ?>
    <div class="card" style="margin-top:20px;">
        <h3>Previsualización del cargue</h3>
        <p class="small" style="margin:6px 0 12px;">Revisa los totales y errores antes de guardar la información en el sistema.</p>
        <div class="resume" style="margin-bottom:12px;">
            <div>
                <span>Responsables detectados</span>
                <strong><?= number_format((int) ($resumen['responsables'] ?? 0), 0, ',', '.') ?></strong>
            </div>
            <div>
                <span>Estudiantes detectados</span>
                <strong><?= number_format((int) ($resumen['estudiantes'] ?? 0), 0, ',', '.') ?></strong>
            </div>
            <div>
                <span>Deudas a registrar</span>
                <strong><?= number_format((int) ($resumen['deudas'] ?? 0), 0, ',', '.') ?></strong>
            </div>
            <div>
                <span>Valor total del archivo</span>
                <strong>$ <?= number_format((float) ($resumen['valor_total'] ?? 0), 0, ',', '.') ?></strong>
            </div>
        </div>
        <div class="table-scroll" style="max-height:220px;margin-bottom:16px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sede</th>
                        <th>Valor total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($totalesSedes as $sede => $valor): ?>
                        <tr>
                            <td><?= htmlspecialchars($sede) ?></td>
                            <td>$ <?= number_format((float) $valor, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($totalesSedes)): ?>
                        <tr><td colspan="2">Sin valores por sede detectados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-scroll" style="max-height:260px;margin-bottom:16px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Valor total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($totalesMeses as $mes => $valor): ?>
                        <tr>
                            <td><?= htmlspecialchars(str_pad((string) $mes, 2, '0', STR_PAD_LEFT)) ?></td>
                            <td>$ <?= number_format((float) $valor, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($totalesMeses)): ?>
                        <tr><td colspan="2">Sin valores monetarios detectados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-scroll" style="max-height:220px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fila</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($errores as $error): ?>
                        <tr>
                            <td><?= htmlspecialchars($error['fila'] ?? '') ?></td>
                            <td><?= htmlspecialchars($error['mensaje'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($errores)): ?>
                        <tr><td colspan="2">No se encontraron errores en la previsualización.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <form method="post" action="index.php?route=carga-masiva/store" style="margin-top:16px;display:flex;justify-content:flex-end;gap:10px;">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="modo" value="confirmar">
            <input type="hidden" name="archivo_temporal" value="<?= htmlspecialchars($preview['archivo_temporal'] ?? '') ?>">
            <input type="hidden" name="archivo_nombre" value="<?= htmlspecialchars($preview['archivo_nombre'] ?? '') ?>">
            <input type="hidden" name="id_colegio" value="<?= htmlspecialchars($preview['id_colegio'] ?? '') ?>">
            <input type="hidden" name="anio" value="<?= htmlspecialchars($preview['anio'] ?? '') ?>">
            <input type="hidden" name="notas" value="<?= htmlspecialchars($preview['notas'] ?? '') ?>">
            <a class="btn secondary" href="index.php?route=carga-masiva">Cancelar</a>
            <button class="btn" type="submit">Guardar cargue</button>
        </form>
    </div>
<?php endif; ?>

<div class="card" style="margin-top:20px;">
    <h3>Historial consolidado</h3>
    <div class="table-scroll" style="max-height:380px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Archivo</th>
                    <th>Resultado</th>
                    <th>Valor cargado ($)</th>
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
                        <td><?= number_format((float) ($carga['total_registros'] ?? 0), 0, ',', '.') ?></td>
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
