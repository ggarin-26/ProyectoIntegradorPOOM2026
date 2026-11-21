<?php
namespace app\controllers;

use app\models\Usuario;

class AutenticacionController {
    private Usuario $usuarios;

    public function __construct() {
        $this->usuarios = new Usuario();
    }

    public function login(): void {
        if (isset($_SESSION['usuario_demo_id'])) {
            $this->redirigirInicio();
        }

        $error = $_SESSION['error_acceso_demo'] ?? null;
        unset($_SESSION['error_acceso_demo']);
        $this->render('auth/login', [
            'usuarios' => $this->usuarios->obtenerActivosParaSelector(),
            'error' => $error,
        ]);
    }

    public function iniciar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirLogin();
        }

        $entradaId = $_POST['usuario_id'] ?? '';
        $id = is_scalar($entradaId) ? filter_var((string) $entradaId, FILTER_VALIDATE_INT) : false;
        $usuario = $id !== false && $id !== null && $id > 0
            ? $this->usuarios->obtenerPorId((int) $id)
            : false;

        if (!$usuario || $usuario['estado'] !== 'Activo'
            || !in_array($usuario['rol'], Usuario::ROLES, true)) {
            $_SESSION['error_acceso_demo'] = 'Seleccioná un usuario activo válido.';
            $this->redirigirLogin();
        }

        session_regenerate_id(true);
        $_SESSION['usuario_demo_id'] = (int) $usuario['id'];
        $this->redirigirInicio();
    }

    public function cerrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirLogin();
        }

        unset($_SESSION['usuario_demo_id'], $_SESSION['usuario_actual']);
        session_regenerate_id(true);
        $this->redirigirLogin();
    }

    private function render(string $vista, array $datos = []): void {
        extract($datos);
        require __DIR__ . '/../views/' . $vista . '.php';
    }

    private function redirigirInicio(): void {
        header('Location: ' . BASE_URL . '/index.php?ctrl=novedades&accion=index');
        exit;
    }

    private function redirigirLogin(): void {
        header('Location: ' . BASE_URL . '/index.php?ctrl=auth&accion=login');
        exit;
    }
}
