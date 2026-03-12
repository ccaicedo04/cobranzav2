<?php
$title = 'Reportes';
$pageTitle = 'Reportes de gestión';
$breadcrumbs = 'Reportes';
include __DIR__ . '/../_partials/header.php';

$columnas = $configReporte['columnas'] ?? [];
$parametros = [
    'tipo' => $tipo,
    'desde' => $filtros['desde'] ?? '',
    'hasta' => $filtros['hasta'] ?? '',
    'estado' => $filtros['estado'] ?? '',
    'metodo' => $filtros['metodo'] ?? '',
];
$parametrosExport = array_filter($parametros, static fn($valor) => $valor !== '' && $valor !== null);
$queryString = http_build_query($parametrosExport, '', '&', PHP_QUERY_RFC3986);
$formatValue = static function (array $fila, array $columna): string {
    $valor = $fila[$columna['campo']] ?? '';
    $formato = $columna['formato'] ?? null;
    if ($formato === 'money') {
        $valor = '$ ' . number_format((float) $valor, 0, ',', '.');
    } elseif ($formato === 'int') {
        $valor = (string) (int) $valor;
    } elseif ($formato === 'date' && !empty($valor)) {
        $timestamp = strtotime((string) $valor);
        $valor = $timestamp ? date('Y-m-d', $timestamp) : (string) $valor;
    }

    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};
?>
<div class="grid grid-2">
    <div class="card">
        <h3>Cartera pendiente</h3>
        <p style="font-size:32px;font-weight:700;">$ <?= number_format($carteraPendiente ?? 0, 0, ',', '.') ?></p>
        <p class="small">Incluye deudas activas y acuerdos pendientes dentro del contexto seleccionado.</p>
    </div>
    <div class="card">
        <h3>Top responsables del contexto</h3>
        <ul class="small" style="margin:0;padding-left:18px;">
            <?php foreach (array_slice($topResponsables ?? [], 0, 5) as $responsable): ?>
                <li><strong><?= htmlspecialchars($responsable['nombre_completo']) ?></strong> — $ <?= number_format($responsable['total'], 0, ',', '.') ?></li>
            <?php endforeach; ?>
            <?php if (empty($topResponsables)): ?>
                <li>No hay responsables con cartera pendiente.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<div class="card" style="margin-bottom:18px;">
    <form class="two" method="get" action="index.php" style="align-items:end;gap:16px;">
        <input type="hidden" name="route" value="reportes">
        <div>
            <label>Tipo de reporte</label>
            <select name="tipo">
                <option value="cartera" <?= $tipo === 'cartera' ? 'selected' : '' ?>>Cartera</option>
                <option value="pagos" <?= $tipo === 'pagos' ? 'selected' : '' ?>>Pagos</option>
                <option value="acuerdos" <?= $tipo === 'acuerdos' ? 'selected' : '' ?>>Acuerdos</option>
            </select>
        </div>
        <div>
            <label>Desde</label>
            <input type="date" name="desde" value="<?= htmlspecialchars($filtros['desde'] ?? '') ?>">
        </div>
        <div>
            <label>Hasta</label>
            <input type="date" name="hasta" value="<?= htmlspecialchars($filtros['hasta'] ?? '') ?>">
        </div>
        <?php if ($tipo === 'pagos'): ?>
            <div>
                <label>Método de pago</label>
                <select name="metodo">
                    <option value="">Todos</option>
                    <?php foreach ($metodosPago as $metodo): ?>
                        <option value="<?= htmlspecialchars($metodo) ?>" <?= ($filtros['metodo'] ?? '') === $metodo ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($metodo)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php else: ?>
            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="pendiente" <?= ($filtros['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_acuerdo" <?= ($filtros['estado'] ?? '') === 'en_acuerdo' ? 'selected' : '' ?>>En acuerdo</option>
                    <option value="pagado" <?= ($filtros['estado'] ?? '') === 'pagado' ? 'selected' : '' ?>>Pagado</option>
                    <option value="activo" <?= ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="cerrado" <?= ($filtros['estado'] ?? '') === 'cerrado' ? 'selected' : '' ?>>Cerrado</option>
                    <option value="incumplido" <?= ($filtros['estado'] ?? '') === 'incumplido' ? 'selected' : '' ?>>Incumplido</option>
                </select>
            </div>
        <?php endif; ?>
        <div style="display:flex;gap:10px;align-items:center;">
            <button class="btn" type="submit">Aplicar filtros</button>
            <a class="btn secondary" href="index.php?route=reportes">Limpiar</a>
        </div>
    </form>
</div>
<div class="card" style="margin-bottom:18px;">
    <h3>Descargar reportes</h3>
    <p class="small">Genera reportes en formatos CSV y PDF listos para compartir con la gerencia.</p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a class="btn" href="index.php?route=reportes/export-excel&amp;<?= htmlspecialchars($queryString) ?>">Exportar CSV</a>
        <a class="btn secondary" href="index.php?route=reportes/export-pdf&amp;<?= htmlspecialchars($queryString) ?>">Exportar PDF</a>
    </div>
</div>
<div class="card">
    <h3><?= htmlspecialchars($configReporte['titulo'] ?? 'Reporte') ?></h3>
    <p class="small" style="margin-top:-6px;"><?= htmlspecialchars($configReporte['descripcion'] ?? '') ?></p>
    <div class="small" style="margin-bottom:12px;color:#1d4ed8;font-weight:600;">Registros encontrados: <?= count($datosReporte) ?></div>
    <table class="table">
        <thead>
            <tr>
                <?php foreach ($columnas as $columna): ?>
                    <th><?= htmlspecialchars($columna['etiqueta']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datosReporte as $fila): ?>
                <tr>
                    <?php foreach ($columnas as $columna): ?>
                        <td><?= $formatValue($fila, $columna) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($datosReporte)): ?>
                <tr><td colspan="<?= count($columnas) ?>">No hay información para los filtros aplicados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
