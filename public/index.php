<?php
/**
 * public/index.php  —  FRONT CONTROLLER
 *
 * Único punto de entrada de la aplicación. Recibe todas las peticiones
 * y las deriva al controlador y acción indicados por URL.
 */

session_start();

// Corregido: apunta correctamente a app/config/config.php desde la carpeta public/
require_once __DIR__ . '/../app/config/config.php';      
require_once __DIR__ . '/../core/autoload.php';

// Lista blanca: solo estos controladores y acciones son accesibles por URL.
$rutasPermitidas = [
    'auth' => [
        'controlador' => \app\controllers\AutenticacionController::class,
        'acciones'    => ['login', 'iniciar', 'cerrar'],
    ],
    'usuarios' => [
        'controlador' => \app\controllers\UsuarioController::class,
        'acciones'    => ['index', 'crear', 'guardar', 'ver', 'editar', 'actualizar', 'eliminar'],
    ],
    'novedades' => [
        'controlador' => \app\controllers\NovedadController::class,
        'acciones'    => [
            'index', 'crear', 'guardar', 'crearSeccion', 'renombrarSeccion', 'ver',
            'editar', 'actualizar', 'eliminar',
        ],
    ],
];

$ctrl   = $_GET['ctrl']   ?? 'novedades';
$accion = $_GET['accion'] ?? 'index';

// Si ctrl o accion llegan como array o con un valor inesperado, muestra 404.
if (!is_string($ctrl) || !is_string($accion)
    || !isset($rutasPermitidas[$ctrl])
    || !in_array($accion, $rutasPermitidas[$ctrl]['acciones'], true)) {
    http_response_code(404);
    echo '404 - Página no encontrada';
    exit;
}

$clase       = $rutasPermitidas[$ctrl]['controlador'];
$controlador = new $clase();

if ($ctrl === 'auth') {
    $controlador->$accion();
    exit;
}

$usuarioId = $_SESSION['usuario_demo_id'] ?? null;
$usuarioActual = is_int($usuarioId) && $usuarioId > 0
    ? (new \app\models\Usuario())->obtenerPorId($usuarioId)
    : false;

if (!$usuarioActual || $usuarioActual['estado'] !== 'Activo'
    || !in_array($usuarioActual['rol'], \app\models\Usuario::ROLES, true)) {
    unset($_SESSION['usuario_demo_id'], $_SESSION['usuario_actual']);
    header('Location: ' . BASE_URL . '/index.php?ctrl=auth&accion=login');
    exit;
}

$_SESSION['usuario_actual'] = [
    'id' => (int) $usuarioActual['id'],
    'nombre' => $usuarioActual['nombre'] . ' ' . $usuarioActual['apellido'],
    'rol' => $usuarioActual['rol'],
];

$permisosPorRol = [
    'Lector' => [
        'novedades' => ['index', 'ver'],
        'usuarios' => ['index', 'ver'],
    ],
    'Editor' => [
        'novedades' => ['index', 'crear', 'guardar', 'ver', 'editar', 'actualizar'],
        'usuarios' => ['index', 'ver'],
    ],
    'Administrador' => [
        'novedades' => ['index', 'crear', 'guardar', 'crearSeccion', 'renombrarSeccion', 'ver', 'editar', 'actualizar', 'eliminar'],
        'usuarios' => ['index', 'crear', 'guardar', 'ver', 'editar', 'actualizar', 'eliminar'],
    ],
];

$accionesDelRol = $permisosPorRol[$usuarioActual['rol']][$ctrl] ?? [];
if (!in_array($accion, $accionesDelRol, true)) {
    http_response_code(403);
    $tituloPagina = 'Acceso denegado';
    $seccionActiva = '';
    require __DIR__ . '/../app/views/auth/acceso_denegado.php';
    exit;
}

$controlador->$accion();