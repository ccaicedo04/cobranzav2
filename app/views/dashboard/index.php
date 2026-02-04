<?php
$title = 'Dashboard';
$pageTitle = 'Dashboard';
$breadcrumbs = 'Inicio / Dashboard';
include __DIR__ . '/../_partials/header.php';
$periodoMeses = (int) ($dashboardMeses ?? 6);
$topLimite = (int) ($dashboardTopLimite ?? 5);
$carteraMesesData = $carteraMeses ?? [];
$recaudoMesesData = $recaudoMeses ?? [];
$trendFromSeries = static function (array $series): array {
    $count = count($series);
    if ($count < 2) {
        return ['value' => 0, 'is_positive' => true];
    }
    $actual = (float) ($series[$count - 1]['total'] ?? 0);
    $previo = (float) ($series[$count - 2]['total'] ?? 0);
    if ($previo == 0.0) {
        return ['value' => 0, 'is_positive' => $actual >= 0];
    }
    $delta = (($actual - $previo) / $previo) * 100;
    return ['value' => $delta, 'is_positive' => $delta >= 0];
};
$trendCartera = $trendFromSeries($carteraMesesData);
$trendRecaudo = $trendFromSeries($recaudoMesesData);
$morosidadPorcentaje = $carteraPendiente && $pagosUltimoMes ? min(100, ($carteraPendiente / max(1, $carteraPendiente + $pagosUltimoMes)) * 100) : 0;
?>
<section class="dashboard-kpis">
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">💼</span>
            <span class="kpi-label">Cartera Total</span>
        </div>
        <div class="kpi-value">$ <?= number_format($carteraPendiente ?? 0, 0, ',', '.') ?></div>
        <div class="kpi-trend <?= $trendCartera['is_positive'] ? 'positive' : 'negative' ?>">
            <?= $trendCartera['is_positive'] ? '▲' : '▼' ?>
            <?= number_format(abs($trendCartera['value']), 1, ',', '.') ?>%
            <span>vs mes anterior</span>
        </div>
        <p class="kpi-footnote">Saldo consolidado.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">💸</span>
            <span class="kpi-label">Recaudo del Mes</span>
        </div>
        <div class="kpi-value">$ <?= number_format($pagosUltimoMes ?? 0, 0, ',', '.') ?></div>
        <div class="kpi-trend <?= $trendRecaudo['is_positive'] ? 'positive' : 'negative' ?>">
            <?= $trendRecaudo['is_positive'] ? '▲' : '▼' ?>
            <?= number_format(abs($trendRecaudo['value']), 1, ',', '.') ?>%
            <span>vs mes anterior</span>
        </div>
        <p class="kpi-footnote">Pagos del mes actual.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">⚠️</span>
            <span class="kpi-label">% Morosidad</span>
        </div>
        <div class="kpi-value"><?= number_format($morosidadPorcentaje, 0, ',', '.') ?>%</div>
        <p class="kpi-footnote"><?= count($topResponsables ?? []) ?> responsables en mora</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">👥</span>
            <span class="kpi-label">Responsables Activos</span>
        </div>
        <div class="kpi-value"><?= count($topResponsables ?? []) ?></div>
        <p class="kpi-footnote">12 nuevos este mes</p>
    </div>
</section>

<section class="dashboard-charts">
    <div class="card chart-card">
        <div class="card-header">
            <h3>Tendencia de Recaudo</h3>
            <span class="chip chip-info">Últimos <?= $periodoMeses ?> meses vs meta</span>
        </div>
        <canvas id="ch3" height="180"></canvas>
    </div>
    <div class="card chart-card">
        <div class="card-header">
            <h3>Estado de Cartera</h3>
            <span class="chip chip-success">Distribución por estado</span>
        </div>
        <canvas id="ch1" height="180"></canvas>
    </div>
</section>

<section class="dashboard-table card">
    <div class="card-header">
        <div>
            <h3>Top <?= $topLimite ?> Responsables con Mayor Deuda</h3>
            <p class="small">Requieren atención prioritaria</p>
        </div>
        <div class="table-actions">
            <a class="btn sm secondary" href="index.php?route=responsables">Ver todos</a>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Responsable</th>
                <th>Total deuda</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($topResponsables ?? []) as $responsable): ?>
                <tr>
                    <td><?= htmlspecialchars($responsable['nombre_completo']) ?></td>
                    <td>$ <?= number_format($responsable['total'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($topResponsables)): ?>
                <tr><td colspan="2">No hay datos disponibles.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<script>
const topResponsables = <?= json_encode(array_map(fn($r) => [
    'nombre' => $r['nombre_completo'],
    'total' => (float)$r['total']
], $topResponsables ?? [])) ?>;
const carteraMeses = <?= json_encode($carteraMeses ?? []) ?>;
const recaudoMeses = <?= json_encode($recaudoMeses ?? []) ?>;
</script>
<script src="assets/js/dashboard.js"></script>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
