<?php
namespace app\models;

use app\config\Database;
use DateTimeImmutable;
use PDO;
use PDOException;
use RuntimeException;

class Novedad {
    private PDO $db;
    private string $nro = '';
    private string $descripcion = '';
    private string $fechaHora = '';
    private int $seccionId = 0;
    private ?int $responsableId = null;

    private const SELECT = "SELECT n.id, n.nro, n.descripcion, n.fecha_hora, n.seccion_id, n.responsable_id,
                                   s.nombre AS seccion_nombre
                            FROM novedades n
                            LEFT JOIN secciones s ON s.id = n.seccion_id";

    public function __construct() {
        $this->db = Database::obtenerInstancia()->obtenerConexion();
    }

    // AQUI se genera de forma automática el número de la novedad basado en la sección y la fecha
    public function crear(): bool {
        try {
            $this->nro = $this->generarNro($this->seccionId, $this->fechaHora);

            $stmt = $this->db->prepare(
                "INSERT INTO novedades (nro, descripcion, fecha_hora, seccion_id, responsable_id)
                 VALUES (:nro, :descripcion, :fecha_hora, :seccion_id, :responsable_id)"
            );
            $stmt->execute([
                ':nro' => $this->nro,
                ':descripcion' => $this->descripcion,
                ':fecha_hora' => $this->fechaHora,
                ':seccion_id' => $this->seccionId,
                ':responsable_id' => $this->responsableId,
            ]);

            return true;
        } catch (PDOException | RuntimeException $e) {
            error_log('Error al crear la novedad: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizar(int $id): bool {
        try {
            $stmt = $this->db->prepare(
                "UPDATE novedades
                 SET descripcion = :descripcion, seccion_id = :seccion_id,
                     responsable_id = :responsable_id
                 WHERE id = :id"
            );
            $stmt->execute([
                ':descripcion' => $this->descripcion,
                ':seccion_id' => $this->seccionId,
                ':responsable_id' => $this->responsableId,
                ':id' => $id,
            ]);
            return true;
        } catch (PDOException | RuntimeException $e) {
            error_log('Error al actualizar la novedad: ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM novedades WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('No se pudo eliminar la novedad seleccionada.');
            }
            return true;
        } catch (PDOException | RuntimeException $e) {
            error_log('Error al eliminar la novedad: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorId(int $id): array | false {
        $stmt = $this->db->prepare(self::SELECT . " WHERE n.id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function buscar(string $termino = '', ?int $seccionId = null): array {
        $sql = self::SELECT . " WHERE 1 = 1";
        $params = [];

        if ($termino !== '') {
            $sql .= " AND (n.nro LIKE :nro OR n.descripcion LIKE :descripcion)";
            $valorBusqueda = '%' . $termino . '%';
            $params[':nro'] = $valorBusqueda;
            $params[':descripcion'] = $valorBusqueda;
        }

        if ($seccionId !== null) {
            $sql .= " AND n.seccion_id = :seccion_id";
            $params[':seccion_id'] = $seccionId;
        }

        $sql .= " ORDER BY n.fecha_hora DESC, n.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerResumen(DateTimeImmutable $inicioMes, DateTimeImmutable $inicioMesSiguiente): array {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(CASE
                        WHEN fecha_hora >= :inicio_mes AND fecha_hora < :fin_mes THEN 1
                        ELSE 0
                    END), 0) AS este_mes
             FROM novedades"
        );
        $stmt->execute([
            ':inicio_mes' => $inicioMes->format('Y-m-d H:i:s'),
            ':fin_mes' => $inicioMesSiguiente->format('Y-m-d H:i:s'),
        ]);
        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resumen === false) {
            throw new RuntimeException('No se pudo obtener el resumen de novedades.');
        }

        return [
            'total' => (int) $resumen['total'],
            'esteMes' => (int) $resumen['este_mes'],
        ];
    }

    public function obtenerSecciones(): array {
        return $this->db
            ->query("SELECT id, nombre FROM secciones ORDER BY nombre")
            ->fetchAll();
    }

    public function existeSeccion(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM secciones WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function crearSeccion(string $nombre): array | false {
        try {
            $duplicada = $this->buscarSeccionPorNombre($nombre);

            if ($duplicada !== false) {
                return [
                    'estado' => 'duplicada',
                    'id' => (int) $duplicada['id'],
                    'nombre' => (string) $duplicada['nombre'],
                ];
            }

            $stmt = $this->db->prepare("INSERT INTO secciones (nombre) VALUES (:nombre)");
            $stmt->execute([':nombre' => $nombre]);
            $id = (int) $this->db->lastInsertId();

            if ($id < 1) {
                throw new RuntimeException('No se pudo recuperar el identificador de la sección creada.');
            }

            return ['estado' => 'creada', 'id' => $id, 'nombre' => $nombre];
        } catch (PDOException | RuntimeException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Error al crear la sección: ' . $e->getMessage());
            return false;
        }
    }

    public function renombrarSeccion(
        int $id,
        string $nombre
    ): array | false {
        try {
            $stmt = $this->db->prepare("SELECT id, nombre FROM secciones WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $seccion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($seccion === false) {
                return ['estado' => 'no_encontrada'];
            }

            $nombreAnterior = (string) $seccion['nombre'];
            if ($nombreAnterior === $nombre) {
                return ['estado' => 'sin_cambios', 'id' => $id, 'nombre' => $nombreAnterior];
            }

            $duplicada = $this->buscarSeccionPorNombre($nombre, $id);
            if ($duplicada !== false) {
                return [
                    'estado' => 'duplicada',
                    'id' => (int) $duplicada['id'],
                    'nombre' => (string) $duplicada['nombre'],
                ];
            }

            $stmt = $this->db->prepare("UPDATE secciones SET nombre = :nombre WHERE id = :id");
            $stmt->execute([':nombre' => $nombre, ':id' => $id]);
            return ['estado' => 'renombrada', 'id' => $id, 'nombre' => $nombre];
        } catch (PDOException | RuntimeException $e) {
            error_log('Error al renombrar la sección: ' . $e->getMessage());
            return false;
        }
    }

    private function buscarSeccionPorNombre(string $nombre, ?int $excluirId = null): array | false {
        $sql = "SELECT id, nombre FROM secciones WHERE nombre = :nombre";
        $parametros = [':nombre' => $nombre];

        if ($excluirId !== null) {
            $sql .= " AND id <> :id";
            $parametros[':id'] = $excluirId;
        }

        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function generarNro(int $seccionId, string $fechaHora): string {
        $stmt = $this->db->prepare("SELECT nombre FROM secciones WHERE id = :id");
        $stmt->execute([':id' => $seccionId]);
        $nombreSeccion = $stmt->fetchColumn();

        if ($nombreSeccion === false || trim($nombreSeccion) === '') {
            throw new RuntimeException('No se encontró el nombre de la sección para generar el Nro.');
        }

        $fecha = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $fechaHora);
        if ($fecha === false) {
            throw new RuntimeException('La fecha de la novedad no es válida para generar el Nro.');
        }

        $iniciales = mb_substr(trim($nombreSeccion), 0, 2, 'UTF-8');
        $iniciales = strtr($iniciales, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N',
        ]);
        $iniciales = mb_strtoupper($iniciales, 'UTF-8');
        $anio = (int) $fecha->format('Y');

        $stmt = $this->db->prepare(
            "INSERT INTO novedades_correlativos (seccion_id, anio, ultimo_numero)
             VALUES (:seccion_id, :anio, 1)
             ON DUPLICATE KEY UPDATE ultimo_numero = ultimo_numero + 1"
        );
        $stmt->execute([':seccion_id' => $seccionId, ':anio' => $anio]);

        $stmt = $this->db->prepare(
            "SELECT ultimo_numero
             FROM novedades_correlativos
             WHERE seccion_id = :seccion_id AND anio = :anio
             FOR UPDATE"
        );
        $stmt->execute([':seccion_id' => $seccionId, ':anio' => $anio]);
        $correlativo = (int) $stmt->fetchColumn();

        return $iniciales . $fecha->format('Ymd') . str_pad((string) $correlativo, 5, '0', STR_PAD_LEFT);
    }

    public function obtenerResponsables(): array {
        return $this->db
            ->query("SELECT id, nombre, apellido, email FROM usuarios ORDER BY apellido, nombre")
            ->fetchAll();
    }

    public function existeResponsable(int $id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function setDescripcion(string $descripcion): void { $this->descripcion = $descripcion; }
    public function setFechaHora(string $fechaHora): void { $this->fechaHora = $fechaHora; }
    public function setSeccionId(int $seccionId): void { $this->seccionId = $seccionId; }
    public function setResponsableId(int $responsableId): void { $this->responsableId = $responsableId; }
}
