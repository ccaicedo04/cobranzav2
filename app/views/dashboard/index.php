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
?>
<section class="dashboard-hero card">
    <div class="dashboard-hero-main">
        <p class="eyebrow">Panel ejecutivo</p>
        <h2>Visión general de la cartera escolar</h2>
        <p class="small">
            Consolida la cartera activa, el recaudo reciente y el comportamiento de responsables para tomar decisiones rápidas en la ventana actual.
        </p>
        <div class="dashboard-hero-actions">
            <a class="btn ghost" href="index.php?route=reportes">Ver reportes</a>
            <a class="btn secondary" href="index.php?route=carga-masiva">Carga masiva</a>
            <a class="btn" href="index.php?route=comunicaciones">Mensajería</a>
        </div>
    </div>
    <div class="dashboard-hero-meta">
        <div>
            <span>Periodo analizado</span>
            <strong>Últimos <?= $periodoMeses ?> meses</strong>
        </div>
        <div>
            <span>Responsables críticos</span>
            <strong>Top <?= $topLimite ?></strong>
        </div>
        <div>
            <span>Estado del panel</span>
            <strong>Actualizado hoy</strong>
        </div>
    </div>
</section>

<section class="dashboard-kpis">
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">💼</span>
            <span class="kpi-label">Cartera activa pendiente</span>
        </div>
        <div class="kpi-value">$ <?= number_format($carteraPendiente ?? 0, 0, ',', '.') ?></div>
        <div class="kpi-trend <?= $trendCartera['is_positive'] ? 'positive' : 'negative' ?>">
            <?= $trendCartera['is_positive'] ? '▲' : '▼' ?>
            <?= number_format(abs($trendCartera['value']), 1, ',', '.') ?>%
            <span>vs mes anterior</span>
        </div>
        <p class="kpi-footnote">Saldo consolidado en estudiantes activos.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">💸</span>
            <span class="kpi-label">Recaudo últimos 30 días</span>
        </div>
        <div class="kpi-value">$ <?= number_format($pagosUltimoMes ?? 0, 0, ',', '.') ?></div>
        <div class="kpi-trend <?= $trendRecaudo['is_positive'] ? 'positive' : 'negative' ?>">
            <?= $trendRecaudo['is_positive'] ? '▲' : '▼' ?>
            <?= number_format(abs($trendRecaudo['value']), 1, ',', '.') ?>%
            <span>vs mes anterior</span>
        </div>
        <p class="kpi-footnote">Pagos aplicados en el último ciclo.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">👥</span>
            <span class="kpi-label">Responsables con deuda</span>
        </div>
        <div class="kpi-value"><?= count($topResponsables ?? []) ?></div>
        <div class="kpi-trend neutral">
            Top <?= $topLimite ?> priorizados
        </div>
        <p class="kpi-footnote">Responsables con mayor exposición.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-card-header">
            <span class="kpi-icon">📈</span>
            <span class="kpi-label">Meses monitoreados</span>
        </div>
        <div class="kpi-value"><?= $periodoMeses ?></div>
        <div class="kpi-trend neutral">
            Datos históricos activos
        </div>
        <p class="kpi-footnote">Histórico visible en tendencias.</p>
    </div>
</section>

<section class="dashboard-charts">
    <div class="card chart-card">
        <div class="card-header">
            <h3>Top <?= $topLimite ?> responsables con mayor deuda</h3>
            <span class="chip chip-danger">Prioridad alta</span>
        </div>
        <p class="small" style="margin:-6px 0 12px;">Enfoca las gestiones en los responsables con mayor exposición.</p>
        <canvas id="ch1" height="180"></canvas>
    </div>
    <div class="card chart-card">
        <div class="card-header">
            <h3>Cartera reportada últimos <?= $periodoMeses ?> meses</h3>
            <span class="chip chip-info">Cartera activa</span>
        </div>
        <p class="small" style="margin:-6px 0 12px;">Comparativo mensual de saldo pendiente.</p>
        <canvas id="ch2" height="180"></canvas>
    </div>
    <div class="card chart-card">
        <div class="card-header">
            <h3>Tendencia de recaudo últimos <?= $periodoMeses ?> meses</h3>
            <span class="chip chip-success">Pagos recibidos</span>
        </div>
        <p class="small" style="margin:-6px 0 12px;">Evolución de pagos registrados en el sistema.</p>
        <canvas id="ch3" height="180"></canvas>
    </div>
</section>

<section class="dashboard-table card">
    <div class="card-header">
        <div>
            <h3>Top responsables</h3>
            <p class="small">Lista de responsables con mayor saldo y nivel de riesgo priorizado.</p>
        </div>
        <div class="table-actions">
            <span class="chip">Actualizado hoy</span>
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

<section class="dashboard-actions">
    <div class="action-card">
        <div class="action-icon success">📲</div>
        <div>
            <h4>Enviar recordatorios</h4>
            <p class="small">Programa comunicaciones multicanal.</p>
        </div>
        <a class="btn sm ghost" href="index.php?route=comunicaciones">Ir a comunicaciones</a>
    </div>
    <div class="action-card">
        <div class="action-icon warning">⚠️</div>
        <div>
            <h4>Cartera crítica</h4>
            <p class="small">Detecta casos vencidos y prioriza gestiones.</p>
        </div>
        <a class="btn sm ghost" href="index.php?route=cartera">Ver cartera</a>
    </div>
    <div class="action-card">
        <div class="action-icon info">📄</div>
        <div>
            <h4>Generar reportes</h4>
            <p class="small">Exporta análisis y reportes ejecutivos.</p>
        </div>
        <a class="btn sm ghost" href="index.php?route=reportes">Ir a reportes</a>
    </div>
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
