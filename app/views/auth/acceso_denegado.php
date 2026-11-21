<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container py-5">
    <div class="app-card p-4">
        <h1 class="h3">Acceso denegado</h1>
        <p class="mb-3">Su privilegio no admite esta acción, comuníquese con el administrador del sistema.</p>
        <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&amp;accion=index" class="btn btn-accent">
            Volver a Novedades
        </a>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
