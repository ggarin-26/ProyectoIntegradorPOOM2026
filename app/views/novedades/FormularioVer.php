<?php
$novedad = $novedad ?? [];
$tituloPagina = 'Detalle de novedad';
$seccionActiva = 'novedades';
$puedeEditarNovedades = in_array(
    $_SESSION['usuario_actual']['rol'] ?? '',
    ['Editor', 'Administrador'],
    true
);
$fechaHora = !empty($novedad['fecha_hora'])
    ? (new DateTime($novedad['fecha_hora']))->format('d/m/Y H:i')
    : '-';

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/encabezado2.php';
?>

<main class="container-fluid px-3 px-lg-4">
    <div class="page-header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=index" class="text-decoration-none">Novedades</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detalle</li>
            </ol>
        </nav>
        <h1>Detalle de novedad <span class="text-muted fw-normal">#<?= (int) ($novedad['id'] ?? 0) ?></span></h1>
    </div>

    <div class="app-card p-3 p-lg-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Nro.</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($novedad['nro'] ?? '') ?>" readonly>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-medium">Fecha y hora</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($fechaHora) ?>" readonly>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Sección</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($novedad['seccion_nombre'] ?? '') ?>" readonly>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Descripción</label>
                <textarea class="form-control" rows="5" readonly><?= htmlspecialchars($novedad['descripcion'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=index" class="btn btn-outline-secondary">Volver</a>
            <?php if ($puedeEditarNovedades): ?>
                <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=editar&id=<?= (int) ($novedad['id'] ?? 0) ?>"
                   class="btn btn-accent">Editar</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
