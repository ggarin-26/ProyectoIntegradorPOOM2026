<?php
/**
 * app/views/usuarios/editar.php
 * Vista del formulario de modificación de usuarios.
 *
 * Variables que inyecta UsuarioController::editar() / actualizar():
 *   $id      (int)   id del usuario que se está editando
 *   $datos   (array) valores de los campos (los de la base, o lo que el usuario corrigió)
 *   $errores (array) errores de validación por campo
 */

$id = isset($id) ? (int) $id : 0;
$tituloPagina  = 'Editar usuario';
$seccionActiva = 'usuarios';
$urlAccion     = BASE_URL . '/index.php?ctrl=usuarios&accion=actualizar&id=' . (int) $id;
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
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=index" class="text-decoration-none">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar usuario</li>
            </ol>
        </nav>
        <h1>Editar usuario <span class="text-muted fw-normal">#<?= (int) $id ?></span></h1>
        <p class="text-muted mb-0" style="font-size:0.88rem;">Modificá los datos y guardá los cambios</p>
    </div>

    <div class="row g-0">
        <div class="col-12 col-lg-7 p-3 p-lg-4">
            <?php require __DIR__ . '/formulario.php'; ?>
        </div>

        <div class="col-12 col-lg-5 d-none d-lg-flex align-items-center justify-content-center bg-transparent text-center p-4">
            <div style="max-width: 240px;">
                <img src="<?= BASE_URL ?>/assets/img/pna-logo.png" 
                    alt="Escudo Prefectura Naval Argentina" 
                    class="img-fluid opacity-25"
                    style="filter: grayscale(100%);">
                <h3 class="text-body-secondary small mt-2 fw-medium">Editar usuario</h3>
            </div>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>