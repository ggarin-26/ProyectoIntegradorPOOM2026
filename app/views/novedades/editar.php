<?php
/**
 * app/views/novedades/editar.php
 * Variables: $datos, $errores, $responsables, $id
 */
$tituloPagina  = 'Editar novedad';
$seccionActiva = 'novedades';
$urlAccion     = BASE_URL . '/index.php?ctrl=novedades&accion=actualizar';
$textoBoton    = 'Guardar cambios';

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/encabezado2.php';
?>

<main class="container-fluid px-3 px-lg-4">

    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=index" class="text-decoration-none">Novedades</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar novedad</li>
            </ol>
        </nav>
        <h1>Editar novedad<?php if (isset($id)): ?> <span class="text-muted fw-normal">#<?= (int) $id ?></span><?php endif; ?></h1>
        <p class="text-muted mb-0" style="font-size:0.88rem;">Modificá los datos y guardá los cambios</p>
    </div>

    <div class="app-card">
        <div class="row g-0">
            <div class="col-12 col-xl-8 p-3 p-lg-4">
                <?php require __DIR__ . '/Formulario.php'; ?>
            </div>

            <div class="col-12 col-xl-4 d-none d-xl-flex align-items-center justify-content-center bg-transparent text-center p-4">
                <div style="max-width: 240px;">
                    <img src="<?= BASE_URL ?>/assets/img/pna-logo.png"
                         alt="Escudo Prefectura Naval Argentina"
                         class="img-fluid opacity-25"
                         style="filter: grayscale(100%);">
                    <h3 class="text-body-secondary small mt-2 fw-medium">Editar novedad</h3>
                </div>
            </div>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>