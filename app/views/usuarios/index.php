<?php
$tituloPagina = "Usuarios";
$seccionActiva = "usuarios";
$mensaje ??= null;
$termino ??= '';
$rol ??= '';
$estado ??= '';
$usuarios ??= [];
$esAdministrador = ($_SESSION['usuario_actual']['rol'] ?? '') === 'Administrador';
$hayFiltros = $termino !== '' || $estado !== '' || $rol !== '';

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/encabezado2.php';
?>

<main class="container-fluid px-3 px-lg-4">
 
    <!-- Encabezado de la sección (breadcrumb + título + acción principal) -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=index" class="text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
                </ol>
            </nav>
            <h1>Gestión de usuarios</h1>
            <p class="text-muted mb-0" style="font-size:0.88rem;">Alta, baja y modificación de registros del sistema</p>
        </div>
        <?php if ($esAdministrador): ?>
        <div>
            <a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=crear" class="btn btn-accent btn-accion-principal">
                <i class="bi bi-plus-lg me-1"></i> Nuevo usuario
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
 
    <!-- Tarjeta con la tabla de datos -->
    <div class="app-card p-3 p-lg-4">
 
        <!-- Buscador y filtro (GET: se aplican al enviar el formulario) -->
        <form method="get" action="<?= BASE_URL ?>/index.php"
              class="user-filter-bar mb-3">
            <input type="hidden" name="ctrl" value="usuarios">
            <input type="hidden" name="accion" value="index">
 
            <div class="input-group user-filter-search" style="max-width: 560px;">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0"
                       placeholder="Buscar por nombre, apellido, dni o correo..."
                       value="<?= htmlspecialchars($termino) ?>">
                <button type="submit" class="btn btn-accent">Buscar</button>
            </div>
 
            <div class="user-filter-role d-flex gap-2">
                <select name="rol" class="form-select" style="max-width: 190px;" onchange="this.form.submit()">
                    <option value="">Todos los roles</option>
                    <?php foreach (\app\models\Usuario::ROLES as $rolOpcion): ?>
                        <option value="<?= htmlspecialchars($rolOpcion) ?>" <?= $rol === $rolOpcion ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rolOpcion) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="user-filter-state d-flex gap-2">
                <select name="estado" class="form-select" style="max-width: 190px;" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="Activo"   <?= $estado === 'Activo'   ? 'selected' : '' ?>>Activo</option>
                    <option value="Inactivo" <?= $estado === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <?php if ($hayFiltros): ?>
                <div class="user-filter-clear">
                    <a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=index"
                       class="btn btn-outline-secondary" title="Quitar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            <?php endif; ?>

        </form>
 
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Apellido</th> 
                        <!-- <th>Correo electrónico</th> -->
                        <!-- <th>DNI</th> -->
                        <th>Fecha Alta</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td>#<?= $usuario['id'] ?></td>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['apellido'] ?? '') ?></td>
                                <!-- <td><?= htmlspecialchars($usuario['email']) ?></td> -->
                                <!-- <td><?= htmlspecialchars(str_pad($usuario['dni'] ?? '', 8, '0', STR_PAD_LEFT)) ?></td> -->
                                <td>
                                    <?= !empty($usuario['fecha_creacion']) ? date('d/m/Y', strtotime($usuario['fecha_creacion'])) : '-' ?>
                                </td>
                                <td><?= htmlspecialchars($usuario['rol']) ?></td>
                                <td>
                                    <span class="badge <?= $usuario['estado'] === 'Activo' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= htmlspecialchars($usuario['estado']) ?>
                                    </span>
                                </td>
                                
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=ver&id=<?= (int) $usuario['id'] ?>"
                                       class="btn btn-sm btn-outline-secondary" title="Ver usuario" aria-label="Ver usuario">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($esAdministrador): ?>
                                    <a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&accion=editar&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario"
                                            data-usuario-id="<?= (int) $usuario['id'] ?>"
                                            data-usuario-nombre="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido'], ENT_QUOTES, 'UTF-8') ?>"
                                            title="Eliminar usuario">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No se encontraron usuarios.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
 
        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
            <span class="text-muted" style="font-size:0.85rem;">
                <?= count($usuarios) ?> <?= count($usuarios) === 1 ? 'registro' : 'registros' ?><?= $hayFiltros ? ' encontrados' : '' ?>
            </span>
        </div>
    </div>

    <?php if ($esAdministrador): ?>
    <div class="modal fade" id="modalEliminarUsuario" tabindex="-1"
         aria-labelledby="modalEliminarUsuarioTitulo" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>/index.php?ctrl=usuarios&amp;accion=eliminar" method="post">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="modalEliminarUsuarioTitulo">Eliminar usuario</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>Se eliminará <strong id="modalEliminarUsuarioNombre"></strong>.</p>
                        <input type="hidden" name="id" id="modalEliminarUsuarioId">
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

<?php if ($esAdministrador): ?>
<script>
    document.getElementById('modalEliminarUsuario').addEventListener('show.bs.modal', event => {
        const boton = event.relatedTarget;
        document.getElementById('modalEliminarUsuarioId').value = boton.dataset.usuarioId;
        document.getElementById('modalEliminarUsuarioNombre').textContent = boton.dataset.usuarioNombre;
    });
</script>
<?php endif; ?>
 
<?php require_once __DIR__ . '/../partials/footer.php'; ?>