<?php
$tituloPagina = 'Seleccionar usuario';
$seccionActiva = '';
$usuarios = $usuarios ?? [];
$error = $error ?? null;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<main class="container py-5">
    <div class="page-header">
        <h1>Seleccionar usuario</h1>
        <p class="text-muted mb-0">Elegí una cuenta para ingresar al sistema de demostración.</p>
    </div>

    <div class="app-card p-3 p-lg-4 mx-auto" style="max-width: 620px;">
        <div class="alert alert-warning" role="alert">
            Este modo no usa contraseña: cualquier persona puede elegir una cuenta y su rol.
            No ofrece protección real y debe usarse solo para demostración local.
        </div>

        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($usuarios === []): ?>
            <p class="text-muted mb-0">No hay usuarios activos disponibles para seleccionar.</p>
        <?php else: ?>
            <form method="post" action="<?= BASE_URL ?>/index.php?ctrl=auth&amp;accion=iniciar">
                <label for="usuario_id" class="form-label">Usuario y rol</label>
                <select id="usuario_id" name="usuario_id" class="form-select mb-3" required>
                    <option value="">Elegí un usuario…</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= (int) $usuario['id'] ?>">
                            <?= htmlspecialchars($usuario['apellido'] . ', ' . $usuario['nombre'] . ' — ' . $usuario['rol']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-accent">Continuar</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
