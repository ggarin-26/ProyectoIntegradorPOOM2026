<?php
namespace app\controllers;
use app\models\Usuario;

// Recibe las peticiones HTTP relacionadas con usuarios
class UsuarioController {
    private Usuario $modelo;

    public function __construct()
    {
        $this->modelo = new Usuario();
    }

    // Función para mostrar el listado de todos los usuarios
    public function index(): void {
        $termino = trim($_GET['q'] ?? '');
        $estado = $_GET['estado'] ?? '';
        $rol = $_GET['rol'] ?? '';

        if (!in_array($estado, ['Activo', 'Inactivo'], true)) {
            $estado = '';
        }

        if (!in_array($rol, Usuario::ROLES, true)) {
            $rol = '';
        }

        $usuarios = ($termino !== '' || $estado !== '' || $rol !== '')
            ? $this->modelo->buscar($termino, $estado, $rol)
            : $this->modelo->obtenerTodos();

        $this->render('usuarios/index', [
            'usuarios' => $usuarios,
            'termino'  => $termino,
            'estado'   => $estado,
            'rol'      => $rol,
            'mensaje'  => $this->tomarMensaje(),
        ]);
    }

    public function ver(): void {
        $id = $this->leerId(INPUT_GET);
        $usuario = $id !== null ? $this->modelo->obtenerPorId($id) : false;

        if (!$usuario) {
            $this->guardarMensaje('danger', 'El usuario que querés consultar no existe');
            $this->redirigir('usuarios', 'index');
        }

        $this->render('usuarios/ver', ['usuario' => $usuario]);
    }

    // Alta nuevo usuario
    public function crear(): void {
        $this->render('usuarios/crear', [
            'datos'   => [
                'nombre' => '', 'apellido' => '', 'email' => '', 'dni' => '',
                'rol' => 'Lector', 'estado' => 'Activo',
            ],
            'errores' => [],
        ]);
    }

    // Función de validar formulario de alta; si es correcto inserta el usuario
    public function guardar(): void {
        $this->exigirPost();
        $datos = $this->leerFormulario();
        $errores = $this->validar($datos);

        if (empty($errores)) {
            $this->cargarModelo($datos);

            if ($this->modelo->crear()) {
                $this->guardarMensaje('success', 'Usuario creado correctamente');
                $this->redirigir('usuarios', 'index');
            }
            $errores['general'] = 'No se pudo guardar el usuario. Intente nuevamente';
        }

        // Hubo errores: se vuelve a mostrar el formulario (corregido la ruta 'usuarios/crear')
        $this->render('usuarios/crear', [
            'datos' => $datos,
            'errores' => $errores,
        ]);
    }

    // Función modificaciones
    public function editar(): void {
        $id = $this->leerId(INPUT_GET);
        $usuario = $id !== null ? $this->modelo->obtenerPorId($id) : false;

        if (!$usuario) {
            $this->guardarMensaje('danger', 'El usuario que querés editar no existe');
            $this->redirigir('usuarios', 'index');
        }

        $this->render('usuarios/editar', [
            'id'      => $id,
            'datos'   => $usuario,
            'errores' => [],
        ]);
    }

    // Validar el formulario de edición y si está correcto actualiza el usuario
    public function actualizar(): void {
        $this->exigirPost();
        $id = $this->leerId(INPUT_GET); 

        if ($id === null || !$this->modelo->obtenerPorId($id)) {
            $this->guardarMensaje('danger', 'El usuario que querés editar no existe');
            $this->redirigir('usuarios', 'index');
        }

        $datos = $this->leerFormulario();
        $errores = $this->validar($datos, $id);

        if (empty($errores)) {
            $this->cargarModelo($datos);

            if ($this->modelo->actualizar($id)) {
                $this->guardarMensaje('success', 'Usuario actualizado correctamente');
                $this->redirigir('usuarios', 'index');
            }
            $errores['general'] = 'No se pudieron actualizar los datos correctamente, intente de nuevo';
        }

        $this->render('usuarios/editar', [
            'id' => $id,
            'datos' => $datos,
            'errores' => $errores,
        ]);
    }

    public function eliminar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('usuarios', 'index');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id <= 0) {
            $this->guardarMensaje('danger', 'El identificador del usuario no es válido');
        } elseif ($this->modelo->eliminar($id)) {
            $this->guardarMensaje('success', 'Usuario eliminado correctamente');
        } else {
            $this->guardarMensaje('danger', 'No se pudo eliminar el usuario');
        }

        $this->redirigir('usuarios', 'index');
    }

    private function validar(array $datos, ?int $idActual = null): array {
        $errores = [];

        // --- Nombre ---
        $largoNombre = mb_strlen($datos['nombre']);
        if ($largoNombre === 0) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        } elseif ($largoNombre < 2) {
            $errores['nombre'] = 'El nombre debe tener al menos 2 caracteres.';
        } elseif ($largoNombre > 100) {
            $errores['nombre'] = 'El nombre no puede superar los 100 caracteres.';
        }

        // --- Apellido ---
        $largoApellido = mb_strlen($datos['apellido']);
        if ($largoApellido === 0) {
            $errores['apellido'] = 'El apellido es obligatorio.';
        } elseif ($largoApellido < 2) {
            $errores['apellido'] = 'El apellido debe tener al menos 2 caracteres.';
        } elseif ($largoApellido > 100) {
            $errores['apellido'] = 'El apellido no puede superar los 100 caracteres.';
        }

        // --- DNI ---
        // --- DNI ---
        $dni = $datos['dni'] ?? '';
        if ($dni === '') {
            $errores['dni'] = 'El DNI es obligatorio.';
        } elseif (mb_strlen($dni) !== 8 || !ctype_digit($dni)) {
            $errores['dni'] = 'El DNI debe contener exactamente 8 dígitos numéricos.';
        }

        // --- Correo ---
        if ($datos['email'] === '') {
            $errores['email'] = 'El correo electrónico es obligatorio.';
        } elseif (mb_strlen($datos['email']) > 150) {
            $errores['email'] = 'El correo no puede superar los 150 caracteres.';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Ingresá un correo con formato válido, por ejemplo nombre@dominio.com.';
        } elseif ($this->modelo->existeEmail($datos['email'], $idActual)) {
            $errores['email'] = 'Ya existe un usuario registrado con ese correo.';
        }

        // --- Rol y estado ---
        if (defined('app\models\Usuario::ROLES') && !in_array($datos['rol'], Usuario::ROLES, true)) {
            $errores['rol'] = 'Elegí un rol de la lista.';
        }
        if (defined('app\models\Usuario::ESTADOS') && !in_array($datos['estado'], Usuario::ESTADOS, true)) {
            $errores['estado'] = 'Elegí un estado de la lista.';
        }

        return $errores;
    }

    // Métodos auxiliares

    /** Lee y limpia los campos del formulario de alta/edición. */
    private function leerFormulario(): array {
        $dniIngresado = trim((string) ($_POST['dni'] ?? ''));

        // Si tiene 7 dígitos numéricos, le anteponemos el '0' automáticamente
        if (mb_strlen($dniIngresado) === 7 && ctype_digit($dniIngresado)) {
            $dniIngresado = '0' . $dniIngresado;
        }

        return [
            'nombre'   => trim((string) ($_POST['nombre'] ?? '')),
            'apellido' => trim((string) ($_POST['apellido'] ?? '')),
            'email'    => strtolower(trim((string) ($_POST['email'] ?? ''))),
            'dni'      => $dniIngresado, // Llega normalizado con 8 dígitos (ej: '06045843')
            'rol'      => (string) ($_POST['rol'] ?? ''),
            'estado'    => (string) ($_POST['estado'] ?? ''),
        ];
    }

    /** Lee el parámetro "id" (GET o POST); devuelve null si no es un entero positivo. */
    private function leerId(int $tipoEntrada): ?int {
        $id = filter_input($tipoEntrada, 'id', FILTER_VALIDATE_INT);
        return ($id !== false && $id !== null && $id > 0) ? $id : null;
    }

    /** Pasa los datos ya validados al modelo. */
   private function cargarModelo(array $datos): void {
        $this->modelo->setNombre($datos['nombre']);
        $this->modelo->setApellido($datos['apellido']); 
        $this->modelo->setEmail($datos['email']);
        $this->modelo->setDni($datos['dni']);
        $this->modelo->setRol($datos['rol']);
        $this->modelo->setEstado($datos['estado']);
    }

    /** Corta la ejecución si la petición no es POST. */
    private function exigirPost(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('usuarios', 'index');
        }
    }

    // Carga una vista y le pasa las variables como si fueran locales
    private function render(string $vista, array $datos = []): void {
        extract($datos);
        require __DIR__ . '/../views/' . $vista . '.php';
    }

    // Redirigir (patrón Post/redirect/Get) y cortar la ejecución
    private function redirigir(string $ctrl, string $accion): void {
        header('Location:' . BASE_URL . "/index.php?ctrl={$ctrl}&accion={$accion}");
        exit;
    }

    // Guardar los mensajes en sesión para mostrarlos en la próxima pantalla
    private function guardarMensaje(string $tipo, string $texto): void {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    // Leer mensaje de sesión y luego lo borra (se muestra una sola vez)
    private function tomarMensaje(): ?array {
        $mensaje = $_SESSION['mensaje'] ?? null;
        unset($_SESSION['mensaje']);
        return $mensaje;
    }
}
?>