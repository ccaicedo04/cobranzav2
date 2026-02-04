<?php
use Core\Session;
$user = Session::get('user');
$title = $title ?? 'Sistema de Cobranza Escolar';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="assets/css/global.css">
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="assets/js/app.js"></script>
</head>
<body>
<?php if ($user): ?>
    <div class="app-shell">
        <?php include __DIR__ . '/navbar.php'; ?>
        <main class="app-main">
<?php endif; ?>
<?php if (empty($fullWidth)): ?>
<div class="app-content">
    <?php if (!$user && !empty($breadcrumbs)): ?>
        <div class="breadcrumbs">
            <?= htmlspecialchars($breadcrumbs) ?>
        </div>
    <?php endif; ?>
    <?php if (!$user && !empty($pageTitle)): ?>
        <h2 class="section"><?= htmlspecialchars($pageTitle) ?></h2>
    <?php endif; ?>
<?php endif; ?>
