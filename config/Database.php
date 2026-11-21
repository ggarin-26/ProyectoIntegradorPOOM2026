<?php
namespace app\config;

use PDO;
use PDOException;

class Database {
    private static ?Database $instancia = null;
    private PDO $conexion;

    private function __construct() {
        // Cargar las credenciales desde el archivo de configuración local
        $configFile = __DIR__ . '/database.local.php';
        
        if (!file_exists($configFile)) {
            die("Error crítico: El archivo de configuración de la base de datos (database.local.php) no existe.");
        }

        $config = require $configFile;

        $host      = $config['host'] ?? 'localhost';
        $puerto    = $config['puerto'] ?? '3306';
        $basedatos = $config['basedatos'] ?? '';
        $usuario   = $config['usuario'] ?? '';
        $clave     = $config['clave'] ?? '';

        $dsn = "mysql:host={$host};port={$puerto};dbname={$basedatos};charset=utf8mb4";
        
        try {
            $this->conexion = new PDO($dsn, $usuario, $clave, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Error al conectar a la base de datos: " . $e->getMessage());
        }
    }

    // Punto de acceso único a la instancia (Patrón Singleton)
    public static function obtenerInstancia(): Database {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia;
    }

    // Devuelve el objeto PDO conectado
    public function obtenerConexion(): PDO {
        return $this->conexion;
    }

    // Evitar que la instancia pueda clonarse
    private function __clone(): void {
    }
}
?>