<?php
if (!isset($seccionActiva)) {
    $seccionActiva = '';
}
$usuarioActual = $_SESSION['usuario_actual'] ?? null;
$esAdministrador = ($usuarioActual['rol'] ?? '') === 'Administrador';

if (!function_exists('claseActiva')) {
    function claseActiva(string $seccion, string $activa): string {
        return $seccion === $activa ? 'active' : '';
    }
}
?>
<nav class="navbar navbar-expand-lg app-navbar">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/index.php">
        <img src="<?= BASE_URL ?>/assets/img/pna.png"
             alt="Logo Sistema ABM"
             width="50"
             height="50"
             class="me-2 d-inline-block align-text-top">
        <span>Sistema Naval de Novedades</span>
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <?php if ($usuarioActual !== null): ?>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= claseActiva('novedades', $seccionActiva) ?>" href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=index">
            <i class="bi bi-journal-text me-1" aria-hidden="true"></i>Novedades
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= claseActiva('usuarios', $seccionActiva) ?>" href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=index">
                <i class="bi bi-people-fill me-1" aria-hidden="true"></i>Usuarios
            </a>
        </li>
      </ul>
      <?php endif; ?>

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if ($usuarioActual !== null): ?>
        <li class="nav-item d-flex align-items-center">
          <span class="nav-link">
            <?= htmlspecialchars($usuarioActual['nombre']) ?>
            <span class="small text-body-secondary">(<?= htmlspecialchars($usuarioActual['rol']) ?> · demo)</span>
          </span>
        </li>
       <li class="nav-item d-flex align-items-center">
          <form method="post" action="<?= BASE_URL ?>/index.php?ctrl=auth&amp;accion=cerrar">
            <button type="submit" class="nav-link">Salir</button>
          </form>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-1 px-2" 
             href="https://www.argentina.gob.ar/prefecturanaval" 
             target="_blank" 
             rel="noopener noreferrer" 
             title="Sitio Oficial Prefectura Naval Argentina">
             Sitio Oficial PNA
             <img src="<?= BASE_URL ?>/assets/img/prefectura-escudo.png"
                  width="70"
                  height="40"
                  alt="Escudo Prefectura Naval Argentina"
                  class="d-inline-block align-text-top">
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>