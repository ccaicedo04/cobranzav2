<?php
$title = 'Dashboard';
$pageTitle = 'Dashboard';
$breadcrumbs = 'Inicio / Dashboard';
include __DIR__ . '/../_partials/header.php';
$periodoMeses = (int) ($dashboardMeses ?? 6);
$topLimite = (int) ($dashboardTopLimite ?? 5);
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
            <a class="btn secondary" href="index.php?route=carga-masiva">Ir a carga masiva</a>
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
        <div class="kpi-label">Cartera activa pendiente</div>
        <div class="kpi-value">$ <?= number_format($carteraPendiente ?? 0, 0, ',', '.') ?></div>
        <p class="kpi-footnote">Saldo consolidado en estudiantes activos.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Recaudo últimos 30 días</div>
        <div class="kpi-value">$ <?= number_format($pagosUltimoMes ?? 0, 0, ',', '.') ?></div>
        <p class="kpi-footnote">Pagos aplicados en el último ciclo.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Responsables con deuda</div>
        <div class="kpi-value"><?= count($topResponsables ?? []) ?></div>
        <p class="kpi-footnote">Responsables con mayor exposición.</p>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Meses monitoreados</div>
        <div class="kpi-value"><?= $periodoMeses ?></div>
        <p class="kpi-footnote">Histórico visible en tendencias.</p>
    </div>
</section>

<section class="dashboard-charts">
    <div class="card chart-card">
        <div class="card-header">
            <h3>Top <?= $topLimite ?> responsables con mayor deuda</h3>
            <span class="chip">Prioridad alta</span>
        </div>
        <canvas id="ch1" height="180"></canvas>
    </div>
    <div class="card chart-card">
        <div class="card-header">
            <h3>Cartera reportada últimos <?= $periodoMeses ?> meses</h3>
            <span class="chip">Cartera activa</span>
        </div>
        <canvas id="ch2" height="180"></canvas>
    </div>
    <div class="card chart-card">
        <div class="card-header">
            <h3>Tendencia de recaudo últimos <?= $periodoMeses ?> meses</h3>
            <span class="chip">Pagos recibidos</span>
        </div>
        <canvas id="ch3" height="180"></canvas>
    </div>
</section>

<section class="dashboard-table card">
    <div class="card-header">
        <div>
            <h3>Top responsables</h3>
            <p class="small">Lista de responsables con mayor saldo y nivel de riesgo priorizado.</p>
        </div>
        <span class="chip">Actualizado hoy</span>
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
