<?php
$title = 'Dashboard';
$pageTitle = 'Dashboard';
$breadcrumbs = 'Inicio / Dashboard';
include __DIR__ . '/../_partials/header.php';
?>
<div class="grid grid-3">
    <div class="card">
        <div class="kpi">Cartera Pendiente</div>
        <strong>$ <?= number_format($carteraPendiente ?? 0, 0, ',', '.') ?></strong>
    </div>
    <div class="card">
        <div class="kpi">Pagos últimos 30 días</div>
        <strong>$ <?= number_format($pagosUltimoMes ?? 0, 0, ',', '.') ?></strong>
    </div>
    <div class="card">
        <div class="kpi">Responsables con deudas</div>
        <strong><?= count($topResponsables ?? []) ?></strong>
    </div>
</div>
<div class="grid grid-3">
    <div class="card chart-card">
        <h3>Top 5 responsables con mayor deuda</h3>
        <canvas id="ch1" height="180"></canvas>
    </div>
    <div class="card chart-card">
        <h3>Cartera reportada últimos 6 meses</h3>
        <canvas id="ch2" height="180"></canvas>
    </div>
    <div class="card chart-card">
        <h3>Tendencia de recaudo últimos 6 meses</h3>
        <canvas id="ch3" height="180"></canvas>
    </div>
</div>
<div class="card">
    <h3>Top responsables</h3>
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
</div>
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
