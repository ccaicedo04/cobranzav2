<?php
$title = 'Detalle estudiante';
$pageTitle = $estudiante['nombre_completo'];
$breadcrumbs = 'Cobranzas / Estudiantes / ' . $estudiante['nombre_completo'];
include __DIR__ . '/../_partials/header.php';
?>
<div class="card">
    <h3>Información general</h3>
    <p><strong>Código:</strong> <?= htmlspecialchars($estudiante['codigo_estudiante']) ?></p>
    <p><strong>Grado:</strong> <?= htmlspecialchars($estudiante['grado']) ?></p>
    <p><strong>Curso:</strong> <?= htmlspecialchars($estudiante['curso']) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($estudiante['estado']) ?></p>
    <div style="margin-top:16px;display:flex;gap:10px;">
        <a class="btn" href="index.php?route=estudiantes/edit&id=<?= $estudiante['id_estudiante'] ?>">Editar</a>
        <form method="post" action="index.php?route=estudiantes/delete" onsubmit="return confirm('¿Eliminar estudiante?');">
            <input type="hidden" name="id" value="<?= $estudiante['id_estudiante'] ?>">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(Core\Helpers::csrfToken()) ?>">
            <button class="btn secondary" type="submit">Eliminar</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../_partials/footer.php'; ?>
