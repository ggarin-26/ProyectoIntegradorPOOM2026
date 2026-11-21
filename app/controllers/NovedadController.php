<?php
namespace app\controllers;
use app\models\Novedad;
use app\models\Usuario;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

//recibe las peticiones http relacionadas con novedades

class NovedadController{
    private Novedad $modelo;

    public function __construct()
    {
        $this->modelo = new Novedad();
    }

    public function index(): void{
        $termino = trim((string) ($_GET['q'] ?? ''));
        $seccionEntrada = $_GET['seccion'] ?? '';
        $seccionId = is_scalar($seccionEntrada)
            ? filter_var((string) $seccionEntrada, FILTER_VALIDATE_INT)
            : false;

        if ($seccionId === false || $seccionId === null || $seccionId < 1
            || !$this->modelo->existeSeccion($seccionId)) {
            $seccionId = null;
        }

        $zonaHoraria = new DateTimeZone('America/Argentina/Buenos_Aires');
        $inicioMes = new DateTimeImmutable('first day of this month 00:00:00', $zonaHoraria);
        $resumenNovedades = $this->modelo->obtenerResumen($inicioMes, $inicioMes->modify('+1 month'));

        $this->render('novedades/index', [
            'novedades' => $this->modelo->buscar($termino, $seccionId),
            'secciones' => $this->modelo->obtenerSecciones(),
            'cantidadUsuarios' => (new Usuario())->contar(),
            'cantidadNovedades' => $resumenNovedades['total'],
            'novedadesEsteMes' => $resumenNovedades['esteMes'],
            'termino'   => $termino,
            'seccionId' => $seccionId,
            'mensaje'   => $this->tomarMensaje(),
        ]);
    }

    //ALTA: formulario vacío con fecha y hora actuales
    public function crear(): void{
        $secciones = $this->modelo->obtenerSecciones();
        $this->mostrarFormulario('novedades/crear', [
            'datos' => [
                'descripcion' => '',
                'fecha_hora' => '',
                'seccion_id' => (string) ($secciones[0]['id'] ?? ''),
                'responsable_id' => '',
            ],
            'errores' => [],
            'usarHoraSistema' => true,
        ]);
    }

    public function guardar(): void{
        $this->exigirPost();
        $datos = $this->leerFormulario();
        $errores = $this->validar($datos);

        if (empty($errores)) {
            $this->cargarModelo($datos);
            if ($this->modelo->crear()) {
                $this->guardarMensaje('success', 'Novedad registrada correctamente');
                $this->redirigir('novedades', 'index');
            }
            $errores['general'] = 'No se pudo guardar la novedad. Intentá nuevamente.';
        }
        $this->mostrarFormulario('novedades/crear', compact('datos', 'errores'));
    }

    public function crearSeccion(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJson(405, ['mensaje' => 'Esta operación requiere una solicitud POST.']);
        }

        $entradaNombre = $_POST['nueva_seccion'] ?? '';
        $nombre = is_string($entradaNombre) ? trim($entradaNombre) : '';
        if ($nombre === '' || mb_strlen($nombre) > 100) {
            $this->responderJson(422, ['mensaje' => 'Ingresá un nombre de sección de hasta 100 caracteres.']);
        }

        $resultado = $this->modelo->crearSeccion($nombre);
        if ($resultado === false) {
            $this->responderJson(500, ['mensaje' => 'No se pudo agregar la sección. Intentá nuevamente.']);
        }
        if ($resultado['estado'] === 'duplicada') {
            $this->responderJson(409, [
                'estado' => 'duplicada',
                'id' => $resultado['id'],
                'nombre' => $resultado['nombre'],
                'mensaje' => 'La sección ya existe en el sistema.',
            ]);
        }

        $this->responderJson(201, $resultado);
    }

    public function renombrarSeccion(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJson(405, ['mensaje' => 'Esta operación requiere una solicitud POST.']);
        }

        $entradaId = $_POST['seccion_existente_id'] ?? '';
        $id = is_scalar($entradaId) ? filter_var((string) $entradaId, FILTER_VALIDATE_INT) : false;
        $entradaNombre = $_POST['nuevo_nombre_seccion'] ?? '';
        $nombre = is_string($entradaNombre) ? trim($entradaNombre) : '';

        if ($id === false || $id === null || $id < 1) {
            $this->responderJson(422, ['mensaje' => 'La sección seleccionada no es válida.']);
        }
        if ($nombre === '' || mb_strlen($nombre) > 100) {
            $this->responderJson(422, ['mensaje' => 'Ingresá un nombre de sección de hasta 100 caracteres.']);
        }

        $resultado = $this->modelo->renombrarSeccion((int) $id, $nombre);
        if ($resultado === false) {
            $this->responderJson(500, ['mensaje' => 'No se pudo modificar la sección. Intentá nuevamente.']);
        }
        if ($resultado['estado'] === 'duplicada') {
            $this->responderJson(409, [
                'estado' => 'duplicada',
                'id' => $resultado['id'],
                'nombre' => $resultado['nombre'],
                'mensaje' => 'Otra sección ya existe con ese nombre.',
            ]);
        }
        if ($resultado['estado'] === 'no_encontrada') {
            $this->responderJson(404, ['mensaje' => 'La sección seleccionada ya no existe.']);
        }
        if ($resultado['estado'] === 'sin_cambios') {
            $this->responderJson(200, $resultado + ['mensaje' => 'El nombre de la sección no cambió.']);
        }

        $this->responderJson(200, $resultado);
    }

    //MODIFICACIÓN
    public function editar(): void{
        $id = $this->leerId(INPUT_GET);
        $novedad = $id !== null ? $this->modelo->obtenerPorId($id) : false;

        if (!$novedad) {
            $this->guardarMensaje('danger', 'La novedad que querés editar no existe');
            $this->redirigir('novedades', 'index');
        }
        $novedad['fecha_hora'] = (new DateTime($novedad['fecha_hora']))->format('Y-m-d\TH:i');
        $this->mostrarFormulario('novedades/editar', ['id' => $id, 'datos' => $novedad, 'errores' => []]);
    }

    public function actualizar(): void{
        $this->exigirPost();
        $id = $this->leerId(INPUT_POST);
        $novedadActual = $id !== null ? $this->modelo->obtenerPorId($id) : false;

        if (!$novedadActual) {
            $this->guardarMensaje('danger', 'La novedad que querés editar no existe');
            $this->redirigir('novedades', 'index');
        }

        $datos = $this->leerFormulario();
        $datos['fecha_hora'] = (new DateTime($novedadActual['fecha_hora']))->format('Y-m-d\TH:i');
        $errores = $this->validar($datos);

        if (empty($errores)) {
            $this->cargarModelo($datos);
            if ($this->modelo->actualizar($id)) {
                $this->guardarMensaje('success', 'Novedad actualizada correctamente');
                $this->redirigir('novedades', 'index');
            }
            $errores['general'] = 'No se pudo actualizar la novedad. Intentá nuevamente.';
        }
        $this->mostrarFormulario('novedades/editar', compact('id', 'datos', 'errores'));
    }

    public function ver(): void{
        $id = $this->leerId(INPUT_GET);
        $novedad = $id !== null ? $this->modelo->obtenerPorId($id) : false;

        if (!$novedad) {
            $this->guardarMensaje('danger', 'La novedad que querés ver no existe');
            $this->redirigir('novedades', 'index');
        }

        $this->render('novedades/FormularioVer', ['novedad' => $novedad]);
    }

    public function eliminar(): void{
        $this->exigirPost();
        $id = $this->leerId(INPUT_POST);
        if ($id === null) {
            $this->guardarMensaje('danger', 'El identificador de la novedad no es válido');
        } elseif ($this->modelo->eliminar($id)) {
            $this->guardarMensaje('success', 'Novedad eliminada correctamente');
        } else {
            $this->guardarMensaje('danger', 'No se pudo eliminar la novedad');
        }
        $this->redirigir('novedades', 'index');
    }

    //VALIDACIÓN: devuelve [campo => mensaje]; vacío = todo correcto
    private function validar(array $datos): array{
        $errores = [];

        $largo = mb_strlen($datos['descripcion']);
        if ($largo === 0) {
            $errores['descripcion'] = 'La descripción es obligatoria.';
        } elseif ($largo < 5) {
            $errores['descripcion'] = 'Describí la novedad con al menos 5 caracteres.';
        } elseif ($largo > 1000) {
            $errores['descripcion'] = 'La descripción no puede superar los 1000 caracteres.';
        }

        if ($datos['fecha_hora'] === '') {
            $errores['fecha_hora'] = 'La fecha y hora son obligatorias.';
        } elseif ($this->parsearFecha($datos['fecha_hora']) === null) {
            $errores['fecha_hora'] = 'Ingresá una fecha y hora válidas.';
        }

        $seccionId = filter_var($datos['seccion_id'], FILTER_VALIDATE_INT);
        if ($seccionId === false || $seccionId === null || $seccionId < 1
            || !$this->modelo->existeSeccion($seccionId)) {
            $errores['seccion_id'] = 'Elegí una sección válida.';
        }

        $responsableId = filter_var($datos['responsable_id'], FILTER_VALIDATE_INT);
        if ($responsableId === false || $responsableId === null || $responsableId < 1
            || !$this->modelo->existeResponsable($responsableId)) {
            $errores['responsable_id'] = 'Elegí un usuario responsable válido.';
        }

        return $errores;
    }

    //MÉTODOS AUXILIARES
    private function leerFormulario(): array{
        return [
            'descripcion'    => trim((string) ($_POST['descripcion'] ?? '')),
            'fecha_hora'     => trim((string) ($_POST['fecha_hora'] ?? '')),
            'seccion_id'     => trim((string) ($_POST['seccion_id'] ?? '')),
            'responsable_id' => trim((string) ($_POST['responsable_id'] ?? '')),
        ];
    }

    //El input datetime-local envía "2026-11-12T14:30"; devuelve null si no es una fecha real
    private function parsearFecha(string $texto): ?DateTime{
        $f = DateTime::createFromFormat('Y-m-d\TH:i', $texto);
        $errs = DateTime::getLastErrors();
        if ($f === false || ($errs && ($errs['warning_count'] > 0 || $errs['error_count'] > 0))) {
            return null;
        }
        return $f;
    }

    private function cargarModelo(array $datos): void{
        $this->modelo->setDescripcion($datos['descripcion']);
        $this->modelo->setFechaHora($this->parsearFecha($datos['fecha_hora'])->format('Y-m-d H:i:s'));
        $this->modelo->setSeccionId((int) $datos['seccion_id']);
        $this->modelo->setResponsableId((int) $datos['responsable_id']);
    }

    //Renderiza un formulario con las secciones disponibles
    private function mostrarFormulario(string $vista, array $datos): void{
        $datos['secciones'] = $this->modelo->obtenerSecciones();
        $datos['responsables'] = $this->modelo->obtenerResponsables();
        $this->render($vista, $datos);
    }

    private function leerId(int $tipoEntrada): ?int{
        $id = filter_input($tipoEntrada, 'id', FILTER_VALIDATE_INT);
        return ($id !== false && $id !== null && $id > 0) ? $id : null;
    }

    private function exigirPost(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('novedades', 'index');
        }
    }

    private function responderJson(int $codigo, array $datos): void{
        http_response_code($codigo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    private function render(string $vista, array $datos = []): void{
        extract($datos);
        require __DIR__ . '/../views/' . $vista . '.php';
    }

    private function redirigir(string $ctrl, string $accion): void{
        header('Location: ' . BASE_URL . "/index.php?ctrl={$ctrl}&accion={$accion}");
        exit;
    }

    private function guardarMensaje(string $tipo, string $texto): void{
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    private function tomarMensaje(): ?array{
        $mensaje = $_SESSION['mensaje'] ?? null;
        unset($_SESSION['mensaje']);
        return $mensaje;
    }
}