<?php
/**
 * app/views/novedades/index.php
 * Variables: $novedades, $secciones, $termino, $seccionId, $mensaje
 */
$tituloPagina = "Novedades";
$seccionActiva = "novedades";
$novedades = $novedades ?? [];
$secciones = $secciones ?? [];
$termino = $termino ?? '';
$seccionId = $seccionId ?? null;
$hayFiltros = $termino !== '' || $seccionId !== null;
$mensaje = $mensaje ?? null;
$cantidadUsuarios = $cantidadUsuarios ?? 0;
$cantidadNovedades = $cantidadNovedades ?? 0;
$novedadesEsteMes = $novedadesEsteMes ?? 0;
$rolActual = $_SESSION['usuario_actual']['rol'] ?? 'Lector';
$puedeEditarNovedades = in_array($rolActual, ['Editor', 'Administrador'], true);
$esAdministrador = $rolActual === 'Administrador';
$seccionesPorId = $secciones;
usort($seccionesPorId, static function (array $a, array $b): int {
    return (int) $a['id'] <=> (int) $b['id'];
});
$huesUsados = [];
$huePorSeccion = [];
foreach ($seccionesPorId as $indice => $seccion) {
    $hue = fmod($indice * 137.508, 360);
    $claveHue = number_format($hue, 2, '.', '');
    while (isset($huesUsados[$claveHue])) {
        $hue = fmod($hue + 1, 360);
        $claveHue = number_format($hue, 2, '.', '');
    }
    $huesUsados[$claveHue] = true;
    $huePorSeccion[(int) $seccion['id']] = $claveHue;
}

// Usamos __DIR__ para asegurar la ruta absoluta correcta
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/encabezado2.php';
?>

<main class="container-fluid px-3 px-lg-4">

    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Novedades</li>
                </ol>
            </nav>
            <h1>Novedades</h1>
            <p class="text-muted mb-0" style="font-size:0.88rem;">Registro de novedades por sección</p>
        </div>
        <?php if ($puedeEditarNovedades): ?>
        <div>
            <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=crear" class="btn btn-accent btn-accion-principal">
                <i class="bi bi-plus-lg me-1"></i> Nueva novedad
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($mensaje['tipo']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensaje['texto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <section id="resumen-reportes" class="mb-4" aria-labelledby="titulo-resumen-reportes">
        <h2 id="titulo-resumen-reportes" class="h5 mb-3">Resumen de reportes</h2>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <article class="app-card p-3 p-lg-4 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="dashboard-stat-icon" aria-hidden="true"><i class="bi bi-people-fill"></i></span>
                        <div>
                            <p class="text-muted mb-1">Usuarios registrados</p>
                            <p class="h3 mb-0"><?= number_format($cantidadUsuarios, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="app-card p-3 p-lg-4 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="dashboard-stat-icon" aria-hidden="true"><i class="bi bi-journal-text"></i></span>
                        <div>
                            <p class="text-muted mb-1">Novedades totales</p>
                            <p class="h3 mb-0"><?= number_format($cantidadNovedades, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="app-card p-3 p-lg-4 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="dashboard-stat-icon" aria-hidden="true"><i class="bi bi-calendar2-month"></i></span>
                        <div>
                            <p class="text-muted mb-1">Novedades de este mes</p>
                            <p class="h3 mb-0"><?= number_format($novedadesEsteMes, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <div class="app-card p-3 p-lg-4 novedades-listado">

        <form method="get" action="<?= BASE_URL ?>/index.php"
              class="novedades-filtros mb-3">
            <input type="hidden" name="ctrl" value="novedades">
            <input type="hidden" name="accion" value="index">

            <div class="input-group novedades-filtros-busqueda">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0"
                       placeholder="Buscar por número o descripción..."
                       value="<?= htmlspecialchars($termino) ?>">
                <button type="submit" class="btn btn-accent">Buscar</button>
            </div>

            <div class="novedades-filtros-seccion">
                <select name="seccion" class="form-select" onchange="this.form.submit()">
                    <option value="">Todas las secciones</option>
                    <?php foreach ($secciones as $s): ?>
                        <option value="<?= (int) $s['id'] ?>" <?= $seccionId === (int) $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($hayFiltros): ?>
                    <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=index"
                       class="btn btn-outline-secondary" title="Quitar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-app novedades-table align-middle mb-0">
                <colgroup>
                    <col class="novedades-col-nro">
                    <col class="novedades-col-fecha">
                    <col class="novedades-col-descripcion">
                    <col class="novedades-col-seccion">
                    <col class="novedades-col-acciones">
                </colgroup>
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Fecha y hora</th>
                        <th>Descripción</th>
                        <th>Sección</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($novedades)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <?= $hayFiltros
                                ? 'Ninguna novedad coincide con la búsqueda. Probá con otro texto o quitá los filtros.'
                                : 'Todavía no hay novedades registradas. Usá «Nueva novedad» para cargar la primera.' ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($novedades as $n): ?>
                    <tr>
                        <td class="text-nowrap"><?= htmlspecialchars($n['nro']) ?></td>
                        <td class="text-nowrap"><?= htmlspecialchars((new DateTime($n['fecha_hora']))->format('d/m/Y H:i')) ?></td>
                        <td style="white-space: pre-line;"><?= htmlspecialchars($n['descripcion']) ?></td>
                        <td class="text-nowrap">
                            <span class="badge badge-seccion"
                                  style="--seccion-hue: <?= $huePorSeccion[(int) ($n['seccion_id'] ?? 0)] ?? 0 ?>;">
                                <?= htmlspecialchars($n['seccion_nombre'] ?? '') ?>
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=ver&id=<?= (int) $n['id'] ?>"
                               class="btn btn-icon btn-outline-secondary me-1" title="Ver" aria-label="Ver novedad">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if ($puedeEditarNovedades): ?>
                            <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=editar&id=<?= (int) $n['id'] ?>"
                               class="btn btn-icon btn-outline-accent me-1" title="Editar" aria-label="Editar novedad">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($esAdministrador): ?>
                            <button type="button" class="btn btn-icon btn-outline-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalEliminarNovedad"
                                    data-novedad-id="<?= (int) $n['id'] ?>"
                                    data-novedad-nro="<?= htmlspecialchars($n['nro'], ENT_QUOTES, 'UTF-8') ?>"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
            <span class="text-muted" style="font-size:0.85rem;">
                <?= count($novedades) ?> <?= count($novedades) === 1 ? 'registro' : 'registros' ?><?= $hayFiltros ? ' encontrados' : '' ?>
            </span>
        </div>
    </div>

    <?php if ($esAdministrador): ?>
    <div class="modal fade" id="modalEliminarNovedad" tabindex="-1"
         aria-labelledby="modalEliminarNovedadTitulo" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>/index.php?ctrl=novedades&amp;accion=eliminar" method="post">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="modalEliminarNovedadTitulo">Eliminar novedad</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>Se eliminará la novedad <strong id="modalEliminarNovedadNro"></strong>.</p>
                        <input type="hidden" name="id" id="modalEliminarNovedadId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-outline-danger">Confirmar baja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<script>
    (() => {
        const reporte = document.getElementById('resumen-reportes');
        if (!reporte) return;

        const alinearReporte = () => {
            if (window.location.hash !== '#resumen-reportes') return;

            const navbar = document.querySelector('.app-navbar');
            const altoNavbar = navbar ? navbar.getBoundingClientRect().height : 0;
            const posicion = reporte.getBoundingClientRect().top + window.scrollY - altoNavbar - 16;
            window.scrollTo({ top: Math.max(0, posicion), behavior: 'auto' });
        };

        const programarAlineacion = () => {
            requestAnimationFrame(() => requestAnimationFrame(alinearReporte));
        };
        programarAlineacion();
        window.addEventListener('hashchange', programarAlineacion);
        window.addEventListener('load', programarAlineacion, { once: true });
    })();
</script>

<?php if ($esAdministrador): ?>
<script>
    document.getElementById('modalEliminarNovedad').addEventListener('show.bs.modal', event => {
        const boton = event.relatedTarget;
        document.getElementById('modalEliminarNovedadId').value = boton.dataset.novedadId;
        document.getElementById('modalEliminarNovedadNro').textContent = boton.dataset.novedadNro;
    });
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>