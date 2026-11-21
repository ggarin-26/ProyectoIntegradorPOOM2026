<?php
$usuario = $usuario ?? [];
$tituloPagina = 'Detalle del usuario';
$seccionActiva = 'usuarios';
$dni = (string) ($usuario['dni'] ?? '');
$fechaCreacion = $usuario['fecha_creacion'] ?? null;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/encabezado2.php';
?>

<main class="container-fluid px-3 px-lg-4">
    <div class="page-header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=index" class="text-decoration-none">Usuarios</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detalle</li>
            </ol>
        </nav>
        <h1>Detalle del usuario <span class="text-muted fw-normal">#<?= (int) $usuario['id'] ?></span></h1>
    </div>

    <div class="row g-0">
        <div class="col-12 col-lg-7 p-3 p-lg-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">Nombre</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" disabled>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">Apellido</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['apellido']) ?>" disabled>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Correo electrónico</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">DNI</label>
                    <input type="text" class="form-control" value="<?= $dni !== '' ? htmlspecialchars(str_pad($dni, 8, '0', STR_PAD_LEFT)) : '-' ?>" disabled>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">Fecha de alta</label>
                    <input type="text" class="form-control"
                           value="<?= $fechaCreacion ? htmlspecialchars((new DateTime($fechaCreacion))->format('d/m/Y')) : '-' ?>"
                           disabled>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">Rol</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['rol']) ?>" disabled>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">Estado</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['estado']) ?>" disabled>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3">
                <a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=index" class="btn btn-outline-secondary">Volver</a>
                <a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=editar&id=<?= (int) $usuario['id'] ?>" class="btn btn-accent">Editar</a>
            </div>
        </div>

        <div class="col-12 col-lg-5 d-none d-lg-flex align-items-center justify-content-center bg-transparent text-center p-4">
            <div style="max-width: 240px;">
                <img src="<?= BASE_URL ?>/assets/img/pna-logo.png"
                     alt="Escudo Prefectura Naval Argentina"
                     class="img-fluid opacity-25"
                     style="filter: grayscale(100%);">
                <h3 class="text-body-secondary small mt-2 fw-medium">Detalle del usuario</h3>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
