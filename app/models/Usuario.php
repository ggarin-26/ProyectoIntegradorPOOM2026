<?php
namespace app\models;

use app\config\Database;
use PDO;
use PDOException;
use RuntimeException;

class Usuario {
    /** Valores permitidos (los usan la validación y los <select> de los formularios) */
    public const ROLES   = ['Administrador', 'Editor', 'Lector'];
    public const ESTADOS = ['Activo', 'Inactivo'];
    private PDO $db;

    // Propiedades del objeto usuario
    private ?int $id = null;
    private string $nombre = "";
    private string $apellido = "";
    private string $email = "";
    private string $rol = "Lector";
    private string $estado = "Activo";
    private string $dni = '';

    public function __construct() {
        // Obtiene la conexión única definida en Database.php
        $this->db = Database::obtenerInstancia()->obtenerConexion();
    }

    // --- MÉTODOS DE ALTA, BAJA, MODIFICACIÓN Y LECTURA ---

    /** Método crear (Alta) */
    public function crear(): bool {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO usuarios (nombre, apellido, email, rol, estado, dni)
                 VALUES (:nombre, :apellido, :email, :rol, :estado, :dni)"
            );
            $stmt->execute([
                ':nombre' => $this->nombre,
                ':apellido' => $this->apellido,
                ':email' => $this->email,
                ':rol' => $this->rol,
                ':estado' => $this->estado,
                ':dni' => $this->dni,
            ]);

            return true;
        } catch (PDOException | RuntimeException $e) {
            error_log("Error al crear el usuario: " . $e->getMessage());
            return false;
        }
    }

    /** Actualizar un usuario existente */
    public function actualizar(int $id): bool {
        try {
            $stmt = $this->db->prepare(
                "UPDATE usuarios
                 SET nombre = :nombre, apellido = :apellido, email = :email,
                     rol = :rol, estado = :estado, dni = :dni
                 WHERE id = :id"
            );
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $this->nombre,
                ':apellido' => $this->apellido,
                ':email' => $this->email,
                ':rol' => $this->rol,
                ':estado' => $this->estado,
                ':dni' => $this->dni,
            ]);

            return true;
        } catch (PDOException | RuntimeException $e) {
            error_log("Error al actualizar el usuario: " . $e->getMessage());
            return false;
        }
    }

    /** Eliminar un usuario */
    public function eliminar(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('No se pudo eliminar el usuario seleccionado.');
            }
            return true;
        } catch (PDOException | RuntimeException $e) {
            error_log("Error al eliminar el usuario: " . $e->getMessage());
            return false;
        }
    }

    /** Lectura de todos los registros */
    public function obtenerTodos(): array {
        $sql = "SELECT id, nombre, apellido, email, dni, rol, estado, fecha_creacion 
                FROM usuarios 
                ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    }

    /** Obtener un usuario por su ID */
    public function obtenerPorId(int $id) {
        $sql = "SELECT id, nombre, apellido, email, dni, rol, estado, fecha_creacion 
                FROM usuarios 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerActivosParaSelector(): array {
        $stmt = $this->db->query(
            "SELECT id, nombre, apellido, rol
             FROM usuarios
             WHERE estado = 'Activo'
             ORDER BY apellido, nombre"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Verificar si un email ya está registrado (omitiendo opcionalmente un ID actual para ediciones) */
    public function existeEmail(string $email, ?int $idActual = null): bool {
        if ($idActual !== null) {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email, 'id' => $idActual]);
        } else {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /** Buscar usuarios por término (nombre/apellido/email/dni), estado y rol */
    public function buscar(string $termino, string $estado, string $rol = ''): array {
        $sql = "SELECT id, nombre, apellido, email, dni, rol, estado, fecha_creacion 
                FROM usuarios 
                WHERE 1=1";
        $params = [];

        if ($termino !== '') {
            $sql .= " AND (nombre LIKE :term1 OR apellido LIKE :term2 OR email LIKE :term3 OR dni LIKE :term4)";
            $valorBusqueda = "%{$termino}%";
            $params['term1'] = $valorBusqueda;
            $params['term2'] = $valorBusqueda;
            $params['term3'] = $valorBusqueda;
            $params['term4'] = $valorBusqueda;
        }

        if ($estado !== '') {
            $sql .= " AND estado = :estado";
            $params['estado'] = $estado;
        }

        if ($rol !== '') {
            $sql .= " AND rol = :rol";
            $params['rol'] = $rol;
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- GETTERS Y SETTERS ---

    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setApellido(string $apellido): void { $this->apellido = $apellido; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setRol(string $rol): void { $this->rol = $rol; }
    public function setEstado(string $estado): void { $this->estado = $estado; }
    public function setDni(string $dni): void { $this->dni = $dni; }

    public function getNombre(): string { return $this->nombre; }
    public function getApellido(): string { return $this->apellido; }
    public function getEmail(): string { return $this->email; }
    public function getRol(): string { return $this->rol; }
    public function getEstado(): string { return $this->estado; }
    public function getDni(): string { return $this->dni; }
}
?>