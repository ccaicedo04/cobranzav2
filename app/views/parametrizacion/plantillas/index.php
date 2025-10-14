<?php
$title = 'Plantillas oficiales';
$pageTitle = 'Formatos y ayudas';
$breadcrumbs = 'Parametrización / Plantillas';
include __DIR__ . '/../../_partials/header.php';
?>
<div class="card">
    <h3>Descargas disponibles</h3>
    <p>Utiliza los siguientes formatos oficiales para cargar información masiva o compartir con tu equipo.</p>
    <ul>
        <li><a class="btn" href="<?= \Core\Helpers::baseUrl('plantillas/plantilla_carga_masiva.php') ?>">Descargar plantilla de carga masiva (.xlsx)</a></li>
    </ul>
</div>
<?php include __DIR__ . '/../../_partials/footer.php'; ?>
